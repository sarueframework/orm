<?php

namespace Sarue\Orm\Tests\Unit\Field;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Field\FieldType\Text\StringFieldType;

#[CoversClass(FieldFactory::class)]
#[UsesClass(StringFieldType::class)]
class FieldFactoryTest extends TestCase
{
    protected EventDispatcherInterface&MockObject $eventDispatcher;
    protected FieldFactory $fieldFactory;

    public function setUp(): void
    {
        $this->fieldFactory = new FieldFactory();
    }

    public function testCreateFromDefinition(): void
    {
        $field = $this->fieldFactory->createFromDefinition('field_name', [
            'type' => 'string',
            'required' => true,
        ]);

        $this->assertInstanceOf(StringFieldType::class, $field);
        $this->assertEquals('field_name', $field->getFieldName());
        $this->assertTrue($field->isRequired());
    }

    public static function dataProviderTestCreateFromDefinitionException(): array
    {
        return [
            [
                '',
                ['type' => 'string'],
                InvalidDefinitionException::class,
                ' field name  should be in snake_case and start with a letter',
            ],
            [
                'FieldName',
                ['type' => 'string'],
                InvalidDefinitionException::class,
                'The field name FieldName should be in snake_case and start with a letter',
            ],
            [
                '_field_name',
                ['type' => 'string'],
                InvalidDefinitionException::class,
                'The field name _field_name should be in snake_case and start with a letter',
            ],
            [
                'field_name',
                [],
                InvalidDefinitionException::class,
                'The field type must be a string.',
            ],
            [
                'field_name',
                ['type' => ['string']],
                InvalidDefinitionException::class,
                'The field type must be a string.',
            ],
            [
                'field_name',
                ['type' => 123],
                InvalidDefinitionException::class,
                'The field type must be a string.',
            ],
            [
                'field_name',
                ['type' => 'invalid_field_type'],
                InvalidDefinitionException::class,
                'invalid_field_type is not a valid field type.',
            ],
        ];
    }

    #[DataProvider('dataProviderTestCreateFromDefinitionException')]
    public function testCreateFromDefinitionException(string $fieldName, array $definition, string $expectedException, string $expectedExceptionMessage): void
    {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->fieldFactory->createFromDefinition($fieldName, $definition);
    }

    public function testCreateFromDefinitionExceptionNotInstance(): void
    {
        $this->expectException(InvalidDefinitionException::class);
        $this->expectExceptionMessage('Class Sarue\Orm\Tests\Unit\Field\FieldFactoryTest is not an instance of \Sarue\Orm\Field\FieldInterface.');

        $factory = new class() extends FieldFactory
        {
            public function resolveClassForFieldType(string $fieldType): string
            {
                return FieldFactoryTest::class;
            }
        };

        $factory->createFromDefinition('field_name', ['type' => 'invalid']);
    }

    public function testCreateFromSchemaStorage(): void
    {
        $field = $this->fieldFactory->createFromSchemaStorage(
            StringFieldType::class,
            'field_name',
            ['schema' => 123],
            ['property' => 123],
            ['additionalOption' => 123],
            false,
        );

        $this->assertInstanceOf(StringFieldType::class, $field);
        $this->assertEquals('field_name', $field->getFieldName());
        $this->assertEquals(['schema' => 123], $field->getSchema());
        $this->assertEquals(['property' => 123], $field->getProperties());
        $this->assertEquals(['additionalOption' => 123], $field->getAdditionalDefinition());
        $this->assertFalse($field->isRequired());
    }

    public function testCreateFromSchemaStorageException(): void
    {
        $this->expectException(InvalidDefinitionException::class);
        $this->expectExceptionMessage('Class Sarue\Orm\Tests\Unit\Field\FieldFactoryTest is not an instance of \Sarue\Orm\Field\FieldInterface.');
        $this->fieldFactory->createFromSchemaStorage(
            self::class,
            'field_name',
            ['schemaOption' => 123],
            ['property' => 123],
            ['additionalOption' => 123],
            false,
        );
    }
}
