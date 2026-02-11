<?php

namespace Sarue\Orm\Internal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Query\Parameter\ParameterInterface;
use Sarue\Orm\Query\QueryInterface;

class OrmDatabaseConnector
{
    public function __construct(
        protected Connection $connection,
    ) {
    }

    public function createTables(array $entityTypeDefinitions): void
    {
        $tables = [];
        foreach ($entityTypeDefinitions as $entityTypeDefinition) {
            $tables = array_merge($tables, $entityTypeDefinition->createTableSchemas());
        }

        $schema = new Schema($tables);
        foreach ($schema->toSql($this->connection->getDatabasePlatform()) as $statement) {
            $this->connection->executeQuery($statement);
        }
    }

    public function loadById(QueryInterface $query, EntityType $entityTypeDefinition, string $id): EntityInterface
    {
        $queryBuilder = $this->createLoadQueryBuilder($query, $entityTypeDefinition->getTableName());
        $queryBuilder->where('id = '.$queryBuilder->createPositionalParameter($id, ParameterType::STRING));
        $entities = $this->doLoadEntities($query, $queryBuilder);
        if (empty($entities)) {
            throw new \Exception('ID not found.');
        }

        return reset($entities);
    }

    public function loadAll(QueryInterface $query, EntityType $entityTypeDefinition): array
    {
        $queryBuilder = $this->createLoadQueryBuilder($query, $entityTypeDefinition->getTableName());
        if ($whereParts = $query->buildSql()) {
            $where = '';
            foreach ($whereParts as $wherePart) {
                if (is_string($wherePart)) {
                    $where .= $wherePart;
                } elseif ($wherePart instanceof ParameterInterface) {
                    $where .= $wherePart->toStringInQuery($queryBuilder);
                } else {
                    throw new \Exception('buildSql returned something not a string or Parameter.');
                }
            }
            $queryBuilder->where($where);
        }

        return $this->doLoadEntities($query, $queryBuilder);
    }

    public function save(EntityInterface $entity, EntityType $entityTypeDefinition): void
    {
        if ($entity->isNew()) {
            $entity->assertInsertAccess();

            if ($entityTypeDefinition->isRevisionable()) {
                $this->connection->beginTransaction();
                $this->saveInsert($entity, $entityTypeDefinition);
                $this->saveRevision($entity, $entityTypeDefinition);
                $this->connection->commit();
            } else {
                $this->saveInsert($entity, $entityTypeDefinition);
            }
        } else {
            $entity->assertUpdateAccess();

            if ($entityTypeDefinition->isRevisionable()) {
                $this->connection->beginTransaction();
                $this->saveUpdate($entity, $entityTypeDefinition);
                $this->saveRevision($entity, $entityTypeDefinition);
                $this->connection->commit();
            } else {
                $this->saveInsert($entity, $entityTypeDefinition);
            }
        }
    }

    protected function saveInsert(EntityInterface $entity, EntityType $entityTypeDefinition): void
    {
        $queryBuilder = $this->createInsertQueryBuilder($entityTypeDefinition, $entity, $entityTypeDefinition->getTableName());
        $sql = $queryBuilder->getSQL();
        $sql .= ' RETURNING id';
        $result = $this->connection->executeQuery($sql, $queryBuilder->getParameters(), $queryBuilder->getParameterTypes());
        $entity->initializeId($result->fetchOne());
    }

    protected function saveUpdate(EntityInterface $entity, EntityType $entityTypeDefinition): void
    {
        $queryBuilder = $this->createUpdateQueryBuilder($entityTypeDefinition, $entity, $entityTypeDefinition->getTableName());
        $queryBuilder->executeQuery();
    }

    protected function saveRevision(EntityInterface $entity, EntityType $entityTypeDefinition): void
    {
        $queryBuilder = $this->createInsertQueryBuilder($entityTypeDefinition, $entity, $entityTypeDefinition->getRevisionTableName());
        $queryBuilder->executeQuery();
    }

    protected function createLoadQueryBuilder(QueryInterface $query, string $table): QueryBuilder
    {
        return $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($table)
        ;
    }

    protected function createInsertQueryBuilder(EntityType $entityTypeDefinition, EntityInterface $entity, string $tableName): QueryBuilder
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->insert($tableName)
        ;

        foreach ($entity->toDatabaseValues() as $columnName => $value) {
            $queryBuilder->setValue($columnName, $value->toStringInQuery($queryBuilder));
        }

        return $queryBuilder;
    }

    protected function createUpdateQueryBuilder(EntityType $entityTypeDefinition, EntityInterface $entity, string $tableName): QueryBuilder
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->update($tableName)
        ;

        foreach ($entity->toDatabaseValues() as $columnName => $value) {
            $queryBuilder->set($columnName, $value->toStringInQuery($queryBuilder));
        }

        return $queryBuilder
            ->where('id='.$queryBuilder->createPositionalParameter($entity->id, ParameterType::STRING));
    }

    protected function doLoadEntities(QueryInterface $query, QueryBuilder $queryBuilder): array
    {
        $results = $queryBuilder->executeQuery()->fetchAllAssociative();
        $entities = [];

        foreach ($results as $result) {
            $entityClass = $query::ENTITY_CLASS;
            $entities[] = $entityClass::fromDatabaseValues($result);
        }

        return $entities;
    }
}
