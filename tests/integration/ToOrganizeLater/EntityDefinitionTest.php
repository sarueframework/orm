<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Sarue\Orm\Exception\EntityDefinition\AbstractEntityTypeException;
use Sarue\Orm\Exception\EntityDefinition\BadInheritanceException;
use Sarue\Orm\Generator\ClassGenerator;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyLogEntity;
use Sarue\Orm\Tests\Integration\Dummy\Entity\NotAnEntity;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionEntity\AbstractEntityType\AbstractEntityTypeEntity;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionEntity\BadInheritance\BadInheritanceEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntityDefinitionTest extends IntegrationTestCase
{
    public function testEntityDefinition(): void
    {
        $entityDefinitions = $this->ormManager->getEntityTypeDefinitions();
        $this->assertCount(1, $entityDefinitions);
        $this->assertArrayHasKey(DummyLogEntity::class, $entityDefinitions);
        $this->assertArrayNotHasKey(NotAnEntity::class, $entityDefinitions);
    }

    public function testBadInheritanceException(): void
    {
        $this->expectException(BadInheritanceException::class);
        $this->expectExceptionMessage('Class '.BadInheritanceEntity::class.' has attribute #[EntityType] but it not a descendant of Sarue\Orm\Entity\AbstractBaseEntity.');

        $classGenerator = new ClassGenerator(
            __DIR__.'/../Dummy/ExceptionEntity/BadInheritance',
            __DIR__.'/../var/sarue-generated',
            'Sarue\\Orm\\Tests\\Integration\\Dummy\\ExceptionEntity\\BadInheritance\\',
        );
        $classGenerator->generateClasses();
    }

    public function testAbstractEntityTypeException(): void
    {
        $this->expectException(AbstractEntityTypeException::class);
        $this->expectExceptionMessage('Class '.AbstractEntityTypeEntity::class.' has attribute #[EntityType] but is abstract.');

        $classGenerator = new ClassGenerator(
            __DIR__.'/../Dummy/ExceptionEntity/AbstractEntityType',
            __DIR__.'/../var/sarue-generated',
            'Sarue\\Orm\\Tests\\Integration\\Dummy\\ExceptionEntity\\AbstractEntityType\\',
        );
        $classGenerator->generateClasses();
    }
}
