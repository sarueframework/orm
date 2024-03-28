<?php

namespace Sarue\Orm\Tests\Integration\EntityType;

use PHPUnit\Framework\Attributes\DataProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sarue\Orm\Event\FieldTypeClassResolutionEvent;
use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Field\FieldBase;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Field\FieldType\Text\StringFieldType;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntityTypeFactoryTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $dispatcher = new class ($this) implements EventDispatcherInterface {
            public function __construct(protected EntityTypeFactoryTest $testClass) {}

            public function dispatch(object $event)
            {
                if ($event instanceof FieldTypeClassResolutionEvent) {
                    switch ($event->getType()) {
                        case 'testable_field':
                            $this->testClass->assertNull($event->getClass());
                            $event->setClass(TestableField::class);
                            break;

                        case 'invalid_field_type':
                            $event->setClass(get_class($this->testClass));
                            break;
                    }
                }
            }
        };

        $this->entityTypeFactory = EntityTypeFactory(new FieldFactory($dispatcher));
    }

    /**
     * Tests instantiation of Entity Type and Fields from a definition, then from storage.
     */
    public function testInstantiation(): void
    {
        $entityTypeFactory = $this->entityTypeFactory();
        $entityType = $entityTypeFactory->createFromDefinition('person', [
            'fields' => [
                'first_name' => [
                    'type' => 'string',
                    'required' => true,
                ],
                'last_name' => [
                    'type' => 'string',
                ],
                'a_test' => [
                    'required' => false,
                    'type' => 'testable_field',
                    'required_schema_option' => 123,
                    'optional_schema_option' => 123,
                    'required_additional_option' => 123,
                    'optional_additional_option' => 123,
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
                        'optional_schema_option' => 123,
                        'required_schema_option' => 123,
                    ],
                    'additional' => [
                        'optional_additional_option' => 123,
                        'required_additional_option' => 123,
                        'type' => 'testable_field',
                    ],
                    'required' => false,
                ],
                'first_name' => [
                    'class' => StringFieldType::class,
                    'schema' => [],
                    'additional' => ['type' => 'string'],
                    'required' => true,
                ],
                'last_name' => [
                    'class' => StringFieldType::class,
                    'schema' => [],
                    'additional' => ['type' => 'string'],
                    'required' => false,
                ],
            ],
        ], $storage);

        // Tests that the
        $entityTypeFromStorage = $entityTypeFactory->createFromSchemaStorage('person', $storage);
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
                'The entity type name  should be in snake_case and start with a letter',
            ],
            [
                'createFromDefinition',
                'Person',
                ['fields' => ['first_name' => ['type' => 'string']]],
                'The entity type name Person should be in snake_case and start with a letter',
            ],
            [
                'createFromDefinition',
                '_person',
                ['fields' => ['first_name' => ['type' => 'string']]],
                'The entity type name _person should be in snake_case and start with a letter',
            ],
            [
                'createFromDefinition',
                'person',
                [],
                'Entity person has no no fields in its definition',
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
                'Option "required" must be boolean.',
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
                'Missing required schema options: required_schema_option',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'a_test' => [
                            'type' => 'testable_field',
                            'required_schema_option' => 123,
                        ],
                    ],
                ],
                'Missing required additional options: required_additional_option',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'first_name' => [],
                    ],
                ],
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
                'The field name firstName should be in snake_case and start with a letter',
            ],
            [
                'createFromSchemaStorage',
                'person',
                [
                    'fields' => [
                        'a_test' => [
                            'class' => self::class,
                            'schema' => [],
                            'additional' => [],
                            'required' => false,
                        ],
                    ],
                ],
                'Class Sarue\Orm\Tests\Integration\EntityType\EntityTypeFactoryTest is not an instance of \Sarue\Orm\Field\FieldInterface.',
            ],
            [
                'createFromDefinition',
                'person',
                [
                    'fields' => [
                        'first_name' => [
                            'type' => 'invalid_field_type',
                        ],
                    ],
                ],
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
                'uknown_field_type is not a valid field type.',
            ],
        ];
    }

    /**
     * Tests errors during instantiation.
     */
    #[DataProvider('dataProviderTestInstantiationExceptions')]
    public function testInstantiationExceptions(string $method, string $entityTypeName, array $definitionOrStorage, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidDefinitionException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $entityTypeFactory = $this->entityTypeFactory();
        $entityTypeFactory->{$method}($entityTypeName, $definitionOrStorage);
    }
}

class TestableField extends FieldBase
{
    protected const array SCHEMA_DEFINITION_OPTIONS = [
        'required_schema_option',
        'optional_schema_option',
    ];
    protected const array REQUIRED_SCHEMA_DEFINITION_OPTIONS = [
        'required_schema_option',
    ];
    protected const array REQUIRED_ADDITIONAL_DEFINITION_OPTIONS = [
        'required_additional_option',
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
