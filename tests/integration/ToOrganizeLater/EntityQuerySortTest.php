<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Sarue\Orm\OrmManager;
use Sarue\Orm\Tests\Integration\Dummy\SimpleEntity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Sort\asc;

class EntityQuerySortTest extends IntegrationTestCase
{
    public function testCombinedOrderBy(): void
    {
        $entity1 = new DummyEntity();
        $entity1->name = 'AAAA';
        $entity1->yearOfBirth = 2000;
        $entity1->save();

        $entity2 = new DummyEntity();
        $entity2->name = 'BBBB';
        $entity2->yearOfBirth = 2000;
        $entity2->save();

        $entity3 = new DummyEntity();
        $entity3->name = 'AAAA';
        $entity3->yearOfBirth = 2020;
        $entity3->save();

        $entity4 = new DummyEntity();
        $entity4->name = 'BBBB';
        $entity4->yearOfBirth = 2020;
        $entity4->save();

        $entities = OrmManager::getInstance()
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->orderBy(
                name: asc(),
                yearOfBirth: asc(),
            )
            ->loadAll();
        $this->assertCount(4, $entities);
        $this->assertEquals('AAAA', $entities[0]->name);
        $this->assertEquals('AAAA', $entities[1]->name);
        $this->assertEquals('BBBB', $entities[2]->name);
        $this->assertEquals('BBBB', $entities[3]->name);
        $this->assertEquals(2000, $entities[0]->yearOfBirth);
        $this->assertEquals(2020, $entities[1]->yearOfBirth);
        $this->assertEquals(2000, $entities[2]->yearOfBirth);
        $this->assertEquals(2020, $entities[3]->yearOfBirth);

        $entities = OrmManager::getInstance()
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->orderBy(
                yearOfBirth: asc(),
                name: asc(),
            )
            ->loadAll();
        $this->assertCount(4, $entities);
        $this->assertEquals('AAAA', $entities[0]->name);
        $this->assertEquals('BBBB', $entities[1]->name);
        $this->assertEquals('AAAA', $entities[2]->name);
        $this->assertEquals('BBBB', $entities[3]->name);
        $this->assertEquals(2000, $entities[0]->yearOfBirth);
        $this->assertEquals(2000, $entities[1]->yearOfBirth);
        $this->assertEquals(2020, $entities[2]->yearOfBirth);
        $this->assertEquals(2020, $entities[3]->yearOfBirth);
    }
}
