<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\EntityDiscoveryCache;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\QueryFactory;

class OrmManager
{
    protected EntityDiscoveryCache $entityDiscoveryCache;
    protected QueryFactory $queryFactory;

    public function createFromContainer() {}

    public function __construct(
        protected Connection $connection,
    ) {}

    public function createTables(): void {
        $tables = [];
        foreach ($this->getEntityDiscoveryCache()->getCachedEntityDefinitions() as $entityTypeName => $definitions) {
            $tableEditor = Table::editor()
                ->setUnquotedName($entityTypeName);

            foreach ($definitions['fields'] as $fieldName => $fieldDefinition) {
                /**
                 * @var \Doctrine\DBAL\Schema\Column[]
                 */
                $columns = [$fieldDefinition['type'], 'getColumns']($fieldName);
                foreach ($columns as $column)                 {
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

    public function getEntityDiscoveryCache(): EntityDiscoveryCache {
        if (!isset($this->entityDiscoveryCache)) {
            $this->entityDiscoveryCache = new EntityDiscoveryCache();
        }

        return $this->entityDiscoveryCache;
    }

    public function getQueryFactory(): QueryFactory {
        if (!isset($this->queryFactory)) {
            $this->queryFactory = new QueryFactory();
        }

        return $this->queryFactory;
    }
}
