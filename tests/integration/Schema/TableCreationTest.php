<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\Entity\Entity;
use Sarue\Orm\Entity\EntityManager;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Schema\TableCreator;
use Sarue\Orm\Tests\Integration\Helper\TestableEntityType;
use Sarue\Orm\Tests\Integration\Helper\TestableField;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class TableCreationTest extends IntegrationTestCase
{

    public function testCreateTable(): void
    {
        $entityType = new TestableEntityType();
        $tableCreator = new TableCreator($this->connection);
        $tableCreator->createTable($entityType);

        $entity = new Entity($entityType, [
            'name' => 'My Company Ltda.',
            'cnpj' => '12345678000199',
        ]);
        $entity->setMainArea('Test Main Area');
        $entityManager = new EntityManager($this->connection);
        $entityManager->save($entity);

        $entity->setMainArea('New Main Area');
        $entityManager->save($entity);
    }
}
