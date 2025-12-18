<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Query\Parameter\ParameterInterface;
use Sarue\Orm\Query\QueryInterface;
use Sarue\Orm\Schema\EntityDefinition;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\EntityDiscoveryCache;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\QueryFactory;

class OrmManager
{
    protected static OrmManager $instance;

    protected EntityDiscoveryCache $entityDiscoveryCache;
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

    public function getEntityDefinition(string $entityClass): EntityDefinition
    {
        return $this->getEntityDiscoveryCache()->getCachedEntityDefinitions()[$entityClass];
    }

    public function getFieldDefinitions(string $entityClass): array
    {
        return $this->getEntityDefinition($entityClass)->fields;
    }

    public function getFieldDefinition(string $entityClass, string $fieldName): FieldTypeInterface
    {
        return $this->getEntityDefinition($entityClass)->fields[$fieldName];
    }

    public function createTables(): void
    {
        $tables = [];
        foreach ($this->getEntityDiscoveryCache()->getCachedEntityDefinitions() as $entityDefinition) {
            $tables[] = $entityDefinition->createTableSchema();
        }

        $schema = new Schema($tables);
        foreach ($schema->toSql($this->connection->getDatabasePlatform()) as $statement) {
            $this->connection->executeQuery($statement);
        }
    }

    public function loadEntities(QueryInterface $query): array
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($this->getEntityDefinition($query::ENTITY_CLASS)->name)
        ;

        $queryBuilder->where();

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    public function save(EntityInterface $entity): void
    {
        $entityDefinition = $this->entityDiscoveryCache->getCachedEntityDefinitions()[get_class($entity)] ?? null;
        if (!$entityDefinition) {
            throw new \Exception('Unknown entity '.get_class($entity));
        }

        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->insert($entityDefinition->name)
        ;

        foreach ($entityDefinition->fields as $fieldDefinition) {
            $fieldDefinition->persistFieldToDatabase($queryBuilder, $entity);
        }

        $queryBuilder->executeQuery();
    }

    public function getEntityDiscoveryCache(): EntityDiscoveryCache
    {
        if (!isset($this->entityDiscoveryCache)) {
            $this->entityDiscoveryCache = new EntityDiscoveryCache();
        }

        return $this->entityDiscoveryCache;
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
        $entityDefinition = $this->getEntityDefinition($query::ENTITY_CLASS);

        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($entityDefinition->name)
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
