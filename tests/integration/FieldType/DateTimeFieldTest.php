<?php

namespace Sarue\Orm\Tests\Integration\Query\Condition;

use Sarue\Orm\OrmManager;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DateTimeDummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class DateTimeFieldTest extends IntegrationTestCase
{
    public function testDateTimeField(): void
    {
        $entity = new DateTimeDummyEntity();
        $entity->date = new \DateTime('2029-05-01');
        $entity->save();

        $loadedEntity = OrmManager::getInstance()
            ->getQueryFactory()
            ->getDateTimeDummyEntityQuery()
            ->loadById($entity->id());

        $this->assertEquals('2029-05-01', $loadedEntity->date->format('Y-m-d'));
    }
}
