<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\EntityManager\Generator\ClassGenerator;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\QueryFactory;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isLargerThan;
use function Sarue\Orm\Query\Condition\startsWith;

class CreateTableTest extends IntegrationTestCase {

    public function testCreateTable(): void {
        $this->ormManager
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->age(isLargerThan(123));
    }

}
