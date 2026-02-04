<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Sarue\Orm\Exception\MayNotDeleteException;
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

    public function testSaveLogEntityException(): void
    {
        $entity1 = new DummyLogEntity();
        $entity1->message = 'Lorem Ipsum';
        $this->ormManager->save($entity1);

        $this->expectException(MayNotDeleteException::class);
        $this->ormManager->save($entity1);
    }
}
