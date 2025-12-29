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
        $entity->name = 'Gustaf Mahler';
        $entity->age = 21;
        $entity->height = new Number('1.75');

        $this->ormManager->save($entity);

        $entity = new DummyEntity();
        $entity->name = 'Louise Farrenc';
        $entity->age = 17;
        $entity->height = new Number('1.85');

        $entity = new DummyEntity();
        $entity->name = 'Franz Schubert';
        $entity->age = 25;
        $entity->height = new Number('1.57');

        $this->ormManager->save($entity);

        $query = $this->ormManager->getQueryFactory()->getDummyEntityQuery();
        $entities = $query
            ->where(
                age: isGreaterThan(20),
            )
            ->and(age: isLessThan(30))
            ->loadAll();

        $this->assertEquals(2, count($entities));
        $this->assertEquals('Gustaf Mahler', $entities[0]->name);
        $this->assertEquals('Franz Schubert', $entities[1]->name);
    }
}
