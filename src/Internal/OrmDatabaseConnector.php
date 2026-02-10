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

    public function save(EntityInterface $entity): void
    {
        if (!$entity->isNew()) {
            $entity->assertUpdateAccess();
        }

        $entityTypeDefinition = $entity->getTypeDefinition();

        $this->connection->beginTransaction();

        $queryBuilder = $this->createInsertQueryBuilder($entityTypeDefinition, $entity, $entityTypeDefinition->getTableName());
        $sql = $queryBuilder->getSQL();
        $sql .= ' RETURNING id';
        $result = $this->connection->executeQuery($sql, $queryBuilder->getParameters(), $queryBuilder->getParameterTypes());

        if ($entityTypeDefinition->isRevisionable()) {
            $queryBuilder = $this->createInsertQueryBuilder($entityTypeDefinition, $entity, $entityTypeDefinition->getRevisionTableName());
            $sql = $queryBuilder->getSQL();
            $this->connection->executeQuery($sql, $queryBuilder->getParameters(), $queryBuilder->getParameterTypes());
            // $sql .= ' RETURNING meta__revisionid';
        }
        $this->connection->commit();

        $entity->initializeId($result->fetchOne());
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

        foreach ($entityTypeDefinition->fields as $fieldDefinition) {
            $fieldDefinition->persistFieldToDatabase($queryBuilder, $entity);
        }

        return $queryBuilder;
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
