<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\Entity\Entity;
use Sarue\Orm\Entity\EntityManager;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Field\Type;
use Sarue\Orm\Schema\TableCreator;
use Sarue\Orm\Tests\Integration\Helper\TestableEntityType;
use Sarue\Orm\Tests\Integration\Helper\TestableField;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class TableCreationTest extends IntegrationTestCase
{
    public function testCreateTable(): void
    {
        $entityType = new EntityType('person', [
            new Type\Text\Text('name', required: true),
            new Type\Numeric\Integer('number', required: true),
            new Type\Numeric\Decimal('salary'),
            new Type\Time\Date('birthdate'),
            new Type\Text\TextMultiple('tags', required: false),
        ]);
        $tableCreator = new TableCreator($this->connection);
        $tableCreator->createTable($entityType);

        $entity = new Entity($entityType, [
            'name' => 'Amram ben Mordechai',
            'number' => 789,
            'tags' => ['blue', 'green'],
        ]);
        $entity->setSalary(11.99);
        $entityManager = new EntityManager($this->connection);
        $entityManager->save($entity);

        $entity->setSalary(55.99);
        $entityManager->save($entity);
    }
}
