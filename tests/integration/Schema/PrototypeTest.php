<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isGreaterThan;
use function Sarue\Orm\Query\Condition\isLessThan;
use function Sarue\Orm\Query\Condition\startsWith;

class PrototypeTest extends IntegrationTestCase
{
    public function testCreateTable(): void
    {
        $entity = new DummyEntity();
        $entity->name = 'John Smith';
        $entity->age = 21;

        $this->ormManager->save($entity);

        $entity = new DummyEntity();
        $entity->name = 'Mary Klein';
        $entity->age = 17;

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
