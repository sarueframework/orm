<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use App\Entity\Manager\DummyEntityManager;
use Sarue\Orm\Schema\ManagerDumper;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class CreateTableTest extends IntegrationTestCase {

    public function testCreateTable(): void {
        $managerDumper = new ManagerDumper(__DIR__ . '/../var/manager');
        $managerDumper->dumpManagerForEntity(DummyEntity::class);




        // $entity = new DummyEntity();
        // $entity->name->set('John Smith');
        // $entity->age->set(900);
    }

}
