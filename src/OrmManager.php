<?php

namespace Sarue\Orm;

use Doctrine\DBAL\Connection;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\QueryFactory;

class OrmManager
{
    protected QueryFactory $queryFactory;

    public function createFromContainer() {}

    public function __construct(
        protected Connection $connection,
    ) {}

    public function createTables(): void {
        $this->getQueryFactory()->getEntityList();
    }

    public function getQueryFactory(): QueryFactory {
        if (!isset($this->queryFactory)) {
            $this->queryFactory = new QueryFactory();
        }

        return $this->queryFactory;
    }
}
