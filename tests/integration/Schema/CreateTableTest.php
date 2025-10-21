<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isLargerThan;
use function Sarue\Orm\Query\Condition\startsWith;

class CreateTableTest extends IntegrationTestCase
{
    public function testCreateTable(): void
    {
        $entity = new DummyEntity();
        $entity->name = 'John Smith';
        $entity->age = 20;

        $this->ormManager->save($entity);

        // $entities = $this->ormManager
        //     ->getQueryFactory()
        //     ->getDummyEntityQuery()
        //     ->age(isLargerThan(18))
        //     ->name(startsWith('B'))
        //     ->loadOne();
    }
}
