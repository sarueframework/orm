<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Doctrine\DBAL\Schema\Table;
use PHPUnit\Framework\Attributes\DataProvider;
use Sarue\Orm\Exception\EntityDefinition\AbstractEntityTypeException;
use Sarue\Orm\Exception\EntityDefinition\BadInheritanceException;
use Sarue\Orm\Exception\EntityDefinition\MultipleAttributesInPropertyException;
use Sarue\Orm\Generator\ClassGenerator;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyLogEntity;
use Sarue\Orm\Tests\Integration\Dummy\Entity\NotAnEntity;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionEntity\AbstractEntityType\AbstractEntityTypeEntity;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionEntity\BadInheritance\BadInheritanceEntity;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionEntity\MultipleAttributesInProperty\MultipleAttributesInPropertyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntityDefinitionTest extends IntegrationTestCase
{
    public function testEntityDefinition(): void
    {
        $entityDefinitions = $this->ormManager->getEntityTypeDefinitions();
        $this->assertCount(1, $entityDefinitions);
        $this->assertArrayHasKey(DummyLogEntity::class, $entityDefinitions);
        $this->assertArrayNotHasKey(NotAnEntity::class, $entityDefinitions);

        $logEntityFieldDefinitions = DummyLogEntity::getFieldDefinitions();
        $this->assertCount(2, $logEntityFieldDefinitions);
        $this->assertArrayHasKey('id', $logEntityFieldDefinitions);
        $this->assertArrayHasKey('message', $logEntityFieldDefinitions);

        $schemaManager = $this->connection->createSchemaManager();

        $tables = $schemaManager->introspectTables();
        $tableNames = array_map(fn (Table $table): string => $table->getObjectName()->toString(), $tables);
        $this->assertCount(1, $tableNames);
        $this->assertContains('"dummylogentity"', $tableNames);
        $this->assertNotContains('"dummylogentity__revision"', $tableNames);
    }

    public static function dataProviderForTestEntityException(): array
    {
        return [
            [
                'BadInheritance',
                BadInheritanceException::class,
                'Class '.BadInheritanceEntity::class.' has attribute #[EntityType] but it not a descendant of Sarue\Orm\Entity\AbstractBaseEntity.',
            ],
            [
                'AbstractEntityType',
                AbstractEntityTypeException::class,
                'Class '.AbstractEntityTypeEntity::class.' has attribute #[EntityType] but is abstract.',
            ],
            [
                'MultipleAttributesInProperty',
                MultipleAttributesInPropertyException::class,
                'Multiple field types have been declared for property "message" in '.MultipleAttributesInPropertyEntity::class.'.',
            ],
        ];
    }

    #[DataProvider('dataProviderForTestEntityException')]
    public function testEntityException($folder, $exceptionClass, $exceptionMessage): void
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $classGenerator = new ClassGenerator(
            __DIR__.'/../Dummy/ExceptionEntity/'.$folder,
            __DIR__.'/../var/sarue-generated',
            'Sarue\\Orm\\Tests\\Integration\\Dummy\\ExceptionEntity\\'.$folder.'\\',
        );
        $classGenerator->generateClasses();
    }
}
