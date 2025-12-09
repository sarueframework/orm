<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use BcMath\Number;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isGreaterThan;
use function Sarue\Orm\Query\Condition\isLessThan;

class PrototypeTest extends IntegrationTestCase
{
    public function testCreateTable(): void
    {
        $entity = new DummyEntity();
        $entity->name = 'John Smith';
        $entity->age = 21;
        $entity->height = new Number('1.75');

        $this->ormManager->save($entity);

        $entity = new DummyEntity();
        $entity->name = 'Mary Klein';
        $entity->age = 17;
        $entity->height = new Number('1.85');

        $this->ormManager->save($entity);

        $query = $this->ormManager->getQueryFactory()->getDummyEntityQuery();
        $entities = $query
            ->where(
                age: isGreaterThan(20),
            )
            ->and(age: isLessThan(30))
            ->loadAll();

        $this->assertEquals(1, count($entities));
        $this->assertEquals('John Smith', $entities[0]->name);
    }
}
