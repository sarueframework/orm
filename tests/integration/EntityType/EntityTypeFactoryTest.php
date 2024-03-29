<?php

namespace Sarue\Orm\Tests\Integration\EntityType;

use PHPUnit\Framework\Attributes\DataProvider;
use Sarue\Orm\Entity\Type\EntityTypeFactory;
use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Exception\InvalidFieldClassException;
use Sarue\Orm\Field\FieldBase;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Field\FieldType\Text\StringFieldType;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntityTypeFactoryTest extends IntegrationTestCase
{
    protected EntityTypeFactory $entityTypeFactory;
    protected FieldFactory $fieldFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->fieldFactory = new class () extends FieldFactory {
            public function resolveClassForFieldType(string $fieldType): string
            {
                return match ($fieldType) {
                    'testable_field' => TestableField::class,
                    'invalid_field' => InvalidClassField::class,
                    'non_type' => EntityTypeFactoryTest::class,
                    default => parent::resolveClassForFieldType($fieldType),
                };
            }
        };
        $this->entityTypeFactory = new EntityTypeFactory($this->fieldFactory);
    }

    /**
     * Tests instantiation of Entity Type and Fields from a definition, then from storage.
     */
    public function testInstantiation(): void
    {
        $entityType = $this->entityTypeFactory->createFromDefinition('person', [
            'fields' => [
                'first_name' => [
                    'type' => 'string',
                    'required' => true,
                ],
                'last_name' => [
                    'type' => 'string',
                    'additional' => [
                        'additional' => 936,
                    ],
                ],
                'a_test' => [
                    'required' => false,
                    'type' => 'testable_field',
                    'requiredSchema' => 123,
                    'optionalSchema' => 456,
                    'requiredProperty' => 789,
                    'optionalProperty' => 987,
                ],
            ],
        ]);

        $storage = $entityType->toStorage();

        // Tests the storage is output corretly and that all arrays have been ordered.
        $this->assertEquals([
            'fields' => [
                'a_test' => [
                    'class' => TestableField::class,
                    'schema' => [
                        'optionalSchema' => 456,
                        'requiredSchema' => 123,
                        'type' => 'testable_field',
                        'ySchema' => 9182,
                        'zSchema' => null,
                    ],
                    'properties' => [
                        'optionalProperty' => 987,
                        'requiredProperty' => 789,
                    ],
                    'additional' => [],
                    'required' => false,
                ],
                'first_name' => [
                    'class' => StringFieldType::class,
                    'schema' => ['type' => 'string'],
                    'properties' => [],
                    'additional' => [],
                    'required' => true,
                ],
                'last_name' => [
                    'class' => StringFieldType::class,
                    'schema' => ['type' => 'string'],
                    'properties' => [],
                    'additional' => ['additional' => 936],
                    'required' => false,
                ],
            ],
        ], $storage);

        // Tests that the
        $entityTypeFromStorage = $this->entityTypeFactory->createFromSchemaStorage('person', $storage);
        $this->assertEquals($storage, $entityTypeFromStorage->toStorage());

        // Tests that the classes have been created correctly.
        $this->assertEquals('person', $entityType->getName());
        $this->assertEquals('person', $entityTypeFromStorage->getName());

        // Tests the field name.
        $this->assertEquals('first_name', $entityType->getField('first_name')->getFieldName());
        $this->assertEquals('last_name', $entityType->getField('last_name')->getFieldName());
        $this->assertEquals('a_test', $entityType->getField('a_test')->getFieldName());
        $this->assertEquals('first_name', $entityTypeFromStorage->getField('first_name')->getFieldName());
        $this->assertEquals('last_name', $entityTypeFromStorage->getField('last_name')->getFieldName());
        $this->assertEquals('a_test', $entityTypeFromStorage->getField('a_test')->getFieldName());

        // Tests the classes of the fields.
        $this->assertInstanceOf(StringFieldType::class, $entityType->getField('first_name'));
        $this->assertInstanceOf(StringFieldType::class, $entityType->getField('last_name'));
        $this->assertInstanceOf(TestableField::class, $entityType->getField('a_test'));
        $this->assertInstanceOf(StringFieldType::class, $entityTypeFromStorage->getField('first_name'));
        $this->assertInstanceOf(StringFieldType::class, $entityTypeFromStorage->getField('last_name'));
        $this->assertInstanceOf(TestableField::class, $entityTypeFromStorage->getField('a_test'));

        // Tests that "required" has been processed corretly.
        $this->assertTrue($entityType->getField('first_name')->isRequired());
        $this->assertFalse($entityType->getField('last_name')->isRequired());
        $this->assertFalse($entityType->getField('a_test')->isRequired());
        $this->assertTrue($entityTypeFromStorage->getField('first_name')->isRequired());
        $this->assertFalse($entityTypeFromStorage->getField('last_name')->isRequired());
        $this->assertFalse($entityTypeFromStorage->getField('a_test')->isRequired());

        // Tests exception in "getField()".
        $this->expectExceptionMessage('Field non_existing_field does not exist in entity person');
        $entityType->getField('non_existing_field');
    }

    public static function dataProviderTestInstantiationExceptions(): array
    {
        return [
            [
                'createFromDefinition',
                '',
                ['fields' => ['first_name' => ['type' => 'string']]],
                InvalidDefinitionException::class,
                'The entity type name  should be in snake_case and start with a letter',
            ],
            [
                'createFromDefinition',
                'Person',
                ['fields' => ['first_name' => ['type' => 'string']]],
                InvalidDefinitionException::class,
                'The entity type name Person should be in snake_case and start with a letter',
            ],
            [
                'createFromDefinition',
                '_person',
                ['fields' => ['first_name' => ['type' => 'string']]],
                InvalidDefinitionException::class,
                'The entity type name _person should be in snake_case and start with a letter',
            ],
            [
                'createFromDefinition',
                'person',
                [],
                InvalidDefinitionException::class,
                'Entity person has no no fields in its definition',
            ],
            [
                'createFromDefinition',
                'person',
                ['fields' => ['field' => ['type' => 'invalid_field']]],
                InvalidFieldClassException::class,
                'Class Sarue\Orm\Tests\Integration\EntityType\InvalidClassField has the same options both in SCHEMA_OPTIONS and PROPERTY_OPTIONS: "option", "option2".',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'a_test' => [
                            'invalidOption' => 'aa',
                            'type' => 'testable_field',
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'Invalid options found: "invalidOption". Valid options are: "invalidOption".',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'first_name' => [
                            'type' => 'string',
                            'required' => 'true',
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'Option "required" must be boolean.',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'first_name' => [
                            'type' => 'string',
                            'additional' => 'something',
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'Option "additional" must be an array.',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'a_test' => [
                            'type' => 'testable_field',
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'Required option "requiredSchema" is missing from definition.',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'a_test' => [
                            'type' => 'testable_field',
                            'requiredSchema' => 123,
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'Required option "requiredProperty" is missing from definition.',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'first_name' => [],
                    ],
                ],
                InvalidDefinitionException::class,
                'The field type must be a string.',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'firstName' => [
                            'type' => 'string',
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'The field name firstName should be in snake_case and start with a letter',
            ],
            [
                'createFromDefinition',
                'person',
                ['fields' => ['a_field' => ['type' => 'non_type']]],
                InvalidDefinitionException::class,
                'Class Sarue\\Orm\\Tests\\Integration\\EntityType\\EntityTypeFactoryTest is not an instance of \\Sarue\\Orm\\Field\\FieldInterface.',
            ],
            [
                'createFromDefinition',
                'person',
                ['fields' => ['a_field' => ['type' => 'non_existing_type']]],
                InvalidDefinitionException::class,
                'non_existing_type is not a valid field type.',
            ],
            [
                'createFromSchemaStorage',
                'person',
                [
                    'fields' => [
                        'a_test' => [
                            'class' => self::class,
                            'schema' => [],
                            'properties' => [],
                            'additional' => [],
                            'required' => false,
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'Class Sarue\Orm\Tests\Integration\EntityType\EntityTypeFactoryTest is not an instance of \Sarue\Orm\Field\FieldInterface.',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'first_name' => [
                            'type' => 'uknown_field_type',
                        ],
                    ],
                ],
                InvalidDefinitionException::class,
                'uknown_field_type is not a valid field type.',
            ],
        ];
    }

    /**
     * Tests errors during instantiation.
     */
    #[DataProvider('dataProviderTestInstantiationExceptions')]
    public function testInstantiationExceptions(string $method, string $entityTypeName, array $definitionOrStorage, string $expectedException, string $expectedExceptionMessage): void
    {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->entityTypeFactory->{$method}($entityTypeName, $definitionOrStorage);
    }
}

class TestableField extends FieldBase
{
    protected const array SCHEMA_OPTIONS = [
        'requiredSchema' => ['required' => true],
        'optionalSchema' => [],
        'ySchema' => ['default' => 9182],
        'zSchema' => [],
    ];
    protected const array PROPERTY_OPTIONS = [
        'requiredProperty' => ['required' => true],
        'optionalProperty' => [],
    ];

    protected static function validateDefinition(
        array $rawDefinition,
        array $schema,
        array $properties,
        array $additionalDefinition,
        bool $required,
    ): void {
        // do nothing
    }
}

class InvalidClassField extends FieldBase
{
    protected const array SCHEMA_OPTIONS = [
        'option' => ['required' => true],
        'option2' => [],
    ];
    protected const array PROPERTY_OPTIONS = [
        'option' => ['required' => true],
        'option2' => [],
    ];

    protected static function validateDefinition(array $rawDefinition, array $schema, array $properties, array $additionalDefinition, bool $required): void
    {
        // do nothing
    }
}
