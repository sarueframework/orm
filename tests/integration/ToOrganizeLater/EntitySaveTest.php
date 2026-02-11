<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Sarue\Orm\Exception\MayNotUpdateException;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyLogEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntitySaveTest extends IntegrationTestCase
{
    public function testSaveLogEntity(): void
    {
        $entity1 = new DummyLogEntity();
        $entity1->message = 'Lorem Ipsum';
        $this->ormManager->save($entity1);

        $entity2 = new DummyLogEntity();
        $entity2->message = 'Qui SitAmet';
        $this->ormManager->save($entity2);

        $loadedEntity1 = $this->ormManager->getQueryFactory()->getDummyLogEntityQuery()->loadById($entity1->id);
        $loadedEntity2 = $this->ormManager->getQueryFactory()->getDummyLogEntityQuery()->loadById($entity2->id);

        $this->assertEquals('Lorem Ipsum', $loadedEntity1->message);
        $this->assertEquals('Qui SitAmet', $loadedEntity2->message);
    }

    public function testSaveRevisionableEntity(): void
    {
        $entity1 = new DummyEntity();
        $entity1->name = 'johann bach';
        $this->ormManager->save($entity1);
        $entity1->name = 'Johann Sebastian Bach';
        $this->ormManager->save($entity1);

        $entity2 = new DummyEntity();
        $entity2->name = 'fanny mendy';
        $this->ormManager->save($entity2);
        $entity2->name = 'Fanny Mendelssohn';
        $this->ormManager->save($entity2);

        $loadedEntity1 = $this->ormManager->getQueryFactory()->getDummyEntityQuery()->loadById($entity1->id);
        $loadedEntity2 = $this->ormManager->getQueryFactory()->getDummyEntityQuery()->loadById($entity2->id);

        $this->assertEquals($entity1->id, $loadedEntity1->id);
        $this->assertEquals('Johann Sebastian Bach', $loadedEntity1->name);
        $this->assertEquals($entity2->id, $loadedEntity2->id);
        $this->assertEquals('Fanny Mendelssohn', $loadedEntity2->name);
    }

    public function testSaveLogEntityException(): void
    {
        $entity1 = new DummyLogEntity();
        $entity1->message = 'Lorem Ipsum';
        $this->ormManager->save($entity1);

        $this->expectException(MayNotUpdateException::class);
        $this->ormManager->save($entity1);
    }
}
