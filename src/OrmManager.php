<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type\FieldTypeInterface;
use Sarue\Orm\Generated\Entity\EntityTypeDefinitionRepository;
use Sarue\Orm\Generated\Entity\Query\QueryFactory;
use Sarue\Orm\Internal\OrmDatabaseConnector;
use Sarue\Orm\Query\QueryInterface;

class OrmManager
{
    protected static OrmManager $instance;

    protected EntityTypeDefinitionRepository $entityTypeDefinitionRepository;
    protected OrmDatabaseConnector $connector;
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
        Connection $connection,
    ) {
        $this->connector = new OrmDatabaseConnector($connection, $this);
        static::setInstance($this);
    }

    // Methods that instantiate subordinate classes.
    // ---------------------------------------------

    public function getEntityTypeDefinitionRepository(): EntityTypeDefinitionRepository
    {
        if (!isset($this->entityTypeDefinitionRepository)) {
            $this->entityTypeDefinitionRepository = new EntityTypeDefinitionRepository();
        }

        return $this->entityTypeDefinitionRepository;
    }

    public function getQueryFactory(): QueryFactory
    {
        if (!isset($this->queryFactory)) {
            $this->queryFactory = new QueryFactory($this);
        }

        return $this->queryFactory;
    }

    // Methods that relay to EntityTypeDefinitionRepository.
    // -----------------------------------------------------

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

    // Methods that relay to OrmDatabaseConnector.
    // -----------------------------------------------------

    public function createTables(): void
    {
        $this->connector->createTables($this->getEntityTypeDefinitions());
    }

    public function loadById(QueryInterface $query, string $id): EntityInterface
    {
        $entityTypeDefinition = $this->getEntityTypeDefinition($query::ENTITY_CLASS);

        return $this->connector->loadById($query, $entityTypeDefinition, $id);
    }

    public function loadAll(QueryInterface $query): array
    {
        $entityTypeDefinition = $this->getEntityTypeDefinition($query::ENTITY_CLASS);

        return $this->connector->loadAll($query, $entityTypeDefinition);
    }

    public function save(EntityInterface $entity): void
    {
        $this->connector->save($entity, $this->getEntityTypeDefinition(get_class($entity)));
    }
}
