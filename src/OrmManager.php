<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Sarue\Orm\Entity\EntityInterface;
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
                /**
                 * @var \Doctrine\DBAL\Schema\Column[]
                 */
                $columns = [$fieldDefinition->type, 'getColumns']($fieldDefinition->name);
                foreach ($columns as $column) {
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

    public function save(EntityInterface $entity): void {
        $entityDefinition = $this->entityDiscoveryCache->getCachedEntityDefinitions()[get_class($entity)] ?? NULL;
        if (!$entityDefinition) {
            throw new \Exception('Unknown entity ' . get_class($entity));
        }

        $query = $this->connection
            ->createQueryBuilder()
            ->insert($entityDefinition->name)
        ;

        foreach ($entityDefinition->fields as $fieldDefinition) {
            $query->setValue($fieldDefinition->name, $query->createNamedParameter($entity->{$fieldDefinition->name}));
        }

        $query->executeQuery();
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
