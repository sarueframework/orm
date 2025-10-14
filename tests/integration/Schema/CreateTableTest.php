<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isLargerThan;

class CreateTableTest extends IntegrationTestCase
{
    public function testCreateTable(): void
    {
        $this->ormManager
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->age(isLargerThan(123));
    }
}
