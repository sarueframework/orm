<?php

namespace Sarue\Orm\Tests\Unit\Entity\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Entity\Type\EntityType;
use Sarue\Orm\Entity\Type\EntityTypeFactory;
use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Field\FieldInterface;
use Sarue\Orm\Field\FieldType\Text\StringFieldType;
use Sarue\Orm\Validator\StringValidator\SnakeCaseValidator;

#[CoversClass(EntityTypeFactory::class)]
#[UsesClass(EntityType::class)]
#[UsesClass(SnakeCaseValidator::class)]
class EntityTypeFactoryTest extends TestCase
{
    protected EntityTypeFactory $entityTypeFactory;
    protected FieldFactory&MockObject $fieldFactory;

    public function setUp(): void
    {
        $this->fieldFactory = $this->createMock(FieldFactory::class);
        $this->entityTypeFactory = new EntityTypeFactory($this->fieldFactory);
    }

    public function testCreateFromDefinition(): void
    {
        $this->fieldFactory
            ->expects($this->once())
            ->method('createFromDefinition')
            ->with($this->identicalTo('field_name'), $this->identicalTo(['type' => 'string']))
            ->willReturn($this->createMock(FieldInterface::class));

        $entityType = $this->entityTypeFactory->createFromDefinition('person', [
            'fields' => [
                'field_name' => ['type' => 'string'],
            ],
        ]);

        $this->assertEquals(['field_name'], array_keys($entityType->getFields()));
        $this->assertInstanceOf(FieldInterface::class, $entityType->getField('field_name'));
    }

    public static function dataProviderTestCreateFromDefinitionExceptions(): array
    {
        return [
            ['Person', [], 'The entity type name Person should be in snake_case and start with a letter.'],
            ['_person', [], 'The entity type name _person should be in snake_case and start with a letter.'],
            ['person', [], 'Entity person has no no fields in its definition.'],
            ['person', ['fields' => []], 'Entity person has no no fields in its definition.'],
        ];
    }

    #[DataProvider('dataProviderTestCreateFromDefinitionExceptions')]
    public function testCreateFromDefinitionExceptions(string $entityTypeName, array $definition, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidDefinitionException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $entityType = $this->entityTypeFactory->createFromDefinition($entityTypeName, $definition);
    }

    public function testCreateFromSchemaStorage(): void
    {
        $this->fieldFactory
            ->expects($this->once())
            ->method('createFromSchemaStorage')
            ->with(
                $this->identicalTo(self::class),
                $this->identicalTo('field_name'),
                $this->identicalTo(['schema' => 123]),
                $this->identicalTo(['properties' => 123]),
                $this->identicalTo(['additional' => 123]),
                $this->identicalTo(true),
            )
            ->willReturn($this->createMock(FieldInterface::class));

        $entityType = $this->entityTypeFactory->createFromSchemaStorage('person', [
            'fields' => [
                'field_name' => [
                    'class' => self::class,
                    'schema' => ['schema' => 123],
                    'properties' => ['properties' => 123],
                    'additional' => ['additional' => 123],
                    'required' => true,
                ],
            ],
        ]);

        $this->assertEquals(['field_name'], array_keys($entityType->getFields()));
        $this->assertInstanceOf(FieldInterface::class, $entityType->getField('field_name'));
    }
}
