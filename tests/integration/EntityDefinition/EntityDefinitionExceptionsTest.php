<?php

namespace Sarue\Orm\Tests\Integration\EntityDefinition;

use PHPUnit\Framework\Attributes\DataProvider;
use Sarue\Orm\Exception\EntityDefinition\AbstractEntityTypeException;
use Sarue\Orm\Exception\EntityDefinition\BadInheritanceException;
use Sarue\Orm\Exception\EntityDefinition\MultipleAttributesInPropertyException;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionAbstractEntityType\AbstractEntityTypeEntity;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionBadInheritance\BadInheritanceEntity;
use Sarue\Orm\Tests\Integration\Dummy\ExceptionMultipleAttributesInProperty\MultipleAttributesInPropertyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntityDefinitionExceptionsTest extends IntegrationTestCase
{
    protected const bool HAS_DATABASE = false;
    protected const bool CREATE_DEFINITIONS = false;

    public static function dataProviderForTestEntityException(): array
    {
        return [
            [
                'ExceptionBadInheritance',
                BadInheritanceException::class,
                'Class '.BadInheritanceEntity::class.' has attribute #[EntityType] but it not a descendant of Sarue\Orm\Entity\AbstractBaseEntity.',
            ],
            [
                'ExceptionAbstractEntityType',
                AbstractEntityTypeException::class,
                'Class '.AbstractEntityTypeEntity::class.' has attribute #[EntityType] but is abstract.',
            ],
            [
                'ExceptionMultipleAttributesInProperty',
                MultipleAttributesInPropertyException::class,
                'Multiple field types have been declared for property "message" in '.MultipleAttributesInPropertyEntity::class.'.',
            ],
        ];
    }

    #[DataProvider('dataProviderForTestEntityException')]
    public function testEntityException(string $entitySet, string $exceptionClass, string $exceptionMessage): void
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        $this->createDefinitions($entitySet);
    }
}
