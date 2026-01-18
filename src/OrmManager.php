<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Query\Parameter\ParameterInterface;
use Sarue\Orm\Query\QueryInterface;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\EntityTypeDefinitionRepository;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\QueryFactory;

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
        $entityTypeDefinition = $this->EntityTypeDefinitionRepository->getEntityTypeDefinitions()[get_class($entity)] ?? null;
        if (!$entityTypeDefinition) {
            throw new \Exception('Unknown entity '.get_class($entity));
        }

        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->insert($entityTypeDefinition->name)
        ;

        foreach ($entityTypeDefinition->fields as $fieldDefinition) {
            $fieldDefinition->persistFieldToDatabase($queryBuilder, $entity);
        }

        $queryBuilder->executeQuery();
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

    public function loadAll(QueryInterface $query): array
    {
        $entityTypeDefinition = $this->getEntityTypeDefinition($query::ENTITY_CLASS);

        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($entityTypeDefinition->name)
        ;

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

        $results = $queryBuilder->executeQuery()->fetchAllAssociative();
        $entities = [];

        foreach ($results as $result) {
            $entityClass = $query::ENTITY_CLASS;
            $entities[] = $entityClass::fromDatabaseValues($result);
        }

        return $entities;
    }
}
