<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Generated\Entity\EntityTypeDefinitionRepository;
use Sarue\Orm\Generated\Entity\Query\QueryFactory;
use Sarue\Orm\Query\Parameter\ParameterInterface;
use Sarue\Orm\Query\QueryInterface;

class OrmManager
{
    protected static OrmManager $instance;

    protected EntityTypeDefinitionRepository $EntityTypeDefinitionRepository;
    protected QueryFactory $queryFactory;

    public static function getInstance(): OrmManager
    {
        if (!isset(static::$instance)) {
            throw new \Exception('ORM is not initialized');
        }

        return static::$instance;
    }

    public static function setInstance(OrmManager $instance): void
    {
        static::$instance = $instance;
    }

    public function __construct(
        protected Connection $connection,
    ) {
        if (!isset(static::$instance)) {
            static::setInstance($this);
        }
    }

    public function getEntityTypeDefinitions(): array
    {
        return $this->getEntityTypeDefinitionRepository()->getEntityTypeDefinitions();
    }

    public function getEntityTypeDefinition(string $entityClass): EntityType
    {
        return $this->getEntityTypeDefinitionRepository()->getEntityTypeDefinition($entityClass);
    }

    public function getFieldDefinitions(string $entityClass): array
    {
        return $this->getEntityTypeDefinitionRepository()->getFieldDefinitions($entityClass);
    }

    public function getFieldDefinition(string $entityClass, string $fieldName): FieldTypeInterface
    {
        return $this->getEntityTypeDefinitionRepository()->getFieldDefinition($entityClass, $fieldName);
    }

    public function createTables(): void
    {
        $tables = [];
        foreach ($this->getEntityTypeDefinitionRepository()->getEntityTypeDefinitions() as $entityTypeDefinition) {
            $tables = array_merge($tables, $entityTypeDefinition->createTableSchemas());
        }

        $schema = new Schema($tables);
        foreach ($schema->toSql($this->connection->getDatabasePlatform()) as $statement) {
            $this->connection->executeQuery($statement);
        }
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

    public function getEntityTypeDefinitionRepository(): EntityTypeDefinitionRepository
    {
        if (!isset($this->EntityTypeDefinitionRepository)) {
            $this->EntityTypeDefinitionRepository = new EntityTypeDefinitionRepository();
        }

        return $this->EntityTypeDefinitionRepository;
    }

    public function getQueryFactory(): QueryFactory
    {
        if (!isset($this->queryFactory)) {
            $this->queryFactory = new QueryFactory($this);
        }

        return $this->queryFactory;
    }

    public function loadById(QueryInterface $query, string $id): EntityInterface
    {
        $queryBuilder = $this->createLoadQueryBuilder($query);
        $queryBuilder->where('id = '.$queryBuilder->createPositionalParameter($id, ParameterType::STRING));
        $entities = $this->doLoadEntities($query, $queryBuilder);
        if (empty($entities)) {
            throw new \Exception('ID not found.');
        }

        return reset($entities);
    }

    public function loadAll(QueryInterface $query): array
    {
        $queryBuilder = $this->createLoadQueryBuilder($query);
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

    protected function createLoadQueryBuilder(QueryInterface $query): QueryBuilder
    {
        $entityTypeDefinition = $this->getEntityTypeDefinition($query::ENTITY_CLASS);

        return $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($entityTypeDefinition->name)
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
