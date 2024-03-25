<?php

namespace Sarue\Orm\Tests\Integration\EntityType;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sarue\Orm\Entity\Type\EntityTypeFactory;
use Sarue\Orm\Event\FieldTypeClassResolutionEvent;
use Sarue\Orm\Field\FieldBase;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Field\FieldType\Text\StringFieldType;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class InstantiationTest extends IntegrationTestCase
{
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
    }

    public function entityTypeFactory(): EntityTypeFactory
    {
        $dispatcher = new class ($this) implements EventDispatcherInterface {
            public function __construct(protected InstantiationTest $testClass) {}

            public function dispatch(object $event)
            {
                if ($event instanceof FieldTypeClassResolutionEvent) {
                    switch ($event->getType()) {
                        case 'testable_field':
                            $this->testClass->assertNull($event->getClass());
                            $event->setClass(TestableField::class);
                            break;
                    }
                }
            }
        };

        return new EntityTypeFactory(new FieldFactory($dispatcher));
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
}
