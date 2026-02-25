<?php

namespace Sarue\Orm\Tests\Integration\EntityDefinition;

use Doctrine\DBAL\Schema\Table;
use Sarue\Orm\OrmManager;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DateTimeDummyEntity;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyLogEntity;
use Sarue\Orm\Tests\Integration\Dummy\Entity\NotAnEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntityDefinitionTest extends IntegrationTestCase
{
    public function testEntityDefinition(): void
    {
        $entityDefinitions = OrmManager::getInstance()->getEntityTypeDefinitions();
        $this->assertCount(3, $entityDefinitions);
        $this->assertArrayHasKey(DummyLogEntity::class, $entityDefinitions);
        $this->assertArrayHasKey(DummyLogEntity::class, $entityDefinitions);
        $this->assertArrayHasKey(DateTimeDummyEntity::class, $entityDefinitions);
        $this->assertArrayNotHasKey(NotAnEntity::class, $entityDefinitions);

        $logEntityFieldDefinitions = DummyLogEntity::getFieldDefinitions();
        $this->assertCount(2, $logEntityFieldDefinitions);
        $this->assertArrayHasKey('id', $logEntityFieldDefinitions);
        $this->assertArrayHasKey('message', $logEntityFieldDefinitions);

        $idFieldDefinition = DummyLogEntity::getFieldDefinition('id');
        $messageFieldDefinition = DummyLogEntity::getFieldDefinition('message');
        $this->assertFalse($idFieldDefinition->isRequired());
        $this->assertTrue($messageFieldDefinition->isRequired());
        $this->assertEquals('string', $idFieldDefinition->getPropertyType());
        $this->assertEquals('string', $messageFieldDefinition->getPropertyType());

        $schemaManager = static::$connection->createSchemaManager();

        $tables = $schemaManager->introspectTables();
        $tableNames = array_map(fn (Table $table): string => $table->getObjectName()->toString(), $tables);
        $this->assertCount(4, $tableNames);
        $this->assertContains('"dummyentity"', $tableNames);
        $this->assertContains('"dummyentity__revision"', $tableNames);
        $this->assertContains('"datetimedummyentity"', $tableNames);
        $this->assertContains('"dummylogentity"', $tableNames);
        $this->assertNotContains('"dummylogentity__revision"', $tableNames);
    }
}
