<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\DummyEntityQuery;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isLargerThan;
use function Sarue\Orm\Query\Condition\startsWith;

class PrototypeTest extends IntegrationTestCase
{
    public function testCreateTable(): void
    {
        $entity = new DummyEntity();
        $entity->name = 'John Smith';
        $entity->age = 20;

        $this->ormManager->save($entity);

        $entity = new DummyEntity();
        $entity->name = 'Mary Klein';
        $entity->age = 17;

        $this->ormManager->save($entity);

        $query = $this->ormManager->getQueryFactory()->getDummyEntityQuery();
        $query
            ->where(
                level: EmployeeLevel::JUNIOR,
                age: isGreaterThanOrEqualTo(20),
            )
            ->and(age: isLesserThan(30))
            ->and($query->orGroup()
                ->or(name: startsWith('B'))
                ->or(name: startsWith('C'))
            )
            ->and(
                $query->join()->department->where(code: is('DRH')),
            );

        $employees = $query->loadAll();

    }
}
