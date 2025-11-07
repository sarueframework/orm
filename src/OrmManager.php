<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Query\QueryInterface;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\EntityDiscoveryCache;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\QueryFactory;

class OrmManager
{
    protected EntityDiscoveryCache $entityDiscoveryCache;
    protected QueryFactory $queryFactory;

    public function __construct(
        protected Connection $connection,
    ) {
    }

    public function createTables(): void
    {
        $tables = [];
        foreach ($this->getEntityDiscoveryCache()->getCachedEntityDefinitions() as $entityDefintion) {
            $tableEditor = Table::editor()
                ->setUnquotedName($entityDefintion->name);

            foreach ($entityDefintion->fields as $fieldDefinition) {
                foreach ($fieldDefinition->getSchema() as $column) {
                    $tableEditor->addColumn($column);
                }
            }

            $tables[] = $tableEditor->create();
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
            ->from($this->getEntityDiscoveryCache()->getCachedEntityDefinitions()[$query::ENTITY_CLASS]->name)
        ;

        $queryBuilder->where();

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    public function save(EntityInterface $entity): void {
        $entityDefinition = $this->entityDiscoveryCache->getCachedEntityDefinitions()[get_class($entity)] ?? NULL;
        if (!$entityDefinition) {
            throw new \Exception('Unknown entity ' . get_class($entity));
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
            $this->queryFactory = new QueryFactory();
        }

        return $this->queryFactory;
    }
}
