<?php

namespace Sarue\Orm\Tests\Unit\Field;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Exception\InvalidFieldClassException;
use Sarue\Orm\Field\FieldBase;

#[CoversClass(FieldBase::class)]
#[UsesClass(TestableFieldBase::class)]
class FieldBaseTest extends TestCase
{
    public static function dataProviderTestParseDefinition(): array
    {
        return [[null], [false], [true]];
    }

    #[DataProvider('dataProviderTestParseDefinition')]
    public function testParseDefinition(?bool $requiredDefinition): void
    {
        $definition = [
            'type' => 'string',
            'requiredSchema' => 123,
            'optionalSchema' => 456,
            'requiredProperty' => 789,
            'optionalProperty' => 987,
            'additional' => [
                'additionalOption' => 123,
            ],
        ];

        if (!is_null($requiredDefinition)) {
            $definition['required'] = $requiredDefinition;
        }

        [$schema, $properties, $additionalDefinition, $required] = TestableFieldBase::parseDefinition('string', $definition);
        $this->assertEquals([
            'optionalSchema' => 456,
            'requiredSchema' => 123,
            'type' => 'string',
        ], $schema);
        $this->assertEquals([
            'optionalProperty' => 987,
            'requiredProperty' => 789,
        ], $properties);
        $this->assertEquals([
            'additionalOption' => 123,
        ], $additionalDefinition);
        $this->assertEquals((bool) $requiredDefinition, $required);
    }

    public static function dataProviderTestParseDefinitionException(): array
    {
        return [
            [
                [
                    'optionalSchema' => 456,
                ],
                InvalidDefinitionException::class,
                'Required option "requiredSchema" is missing from definition.',
            ],
            [
                [
                    'requiredSchema' => 123,
                ],
                InvalidDefinitionException::class,
                'Required option "requiredProperty" is missing from definition.',
            ],
            [
                [
                    'requiredSchema' => 123,
                    'requiredProperty' => 123,
                    'invalidOption' => 123,
                ],
                InvalidDefinitionException::class,
                'Invalid options found: "invalidOption". Valid options are: "invalidOption".',
            ],
            [
                [
                    'requiredSchema' => 123,
                    'requiredProperty' => 123,
                    'additional' => 'aaa',
                ],
                InvalidDefinitionException::class,
                'Option "additional" must be an array.',
            ],
            [
                [
                    'requiredSchema' => 123,
                    'requiredProperty' => 123,
                    'required' => 'true',
                ],
                InvalidDefinitionException::class,
                'Option "required" must be boolean.',
            ],
        ];
    }

    #[DataProvider('dataProviderTestParseDefinitionException')]
    public function testParseDefinitionException(array $definition, string $expectedException, string $expectedExceptionMessage): void
    {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        TestableFieldBase::parseDefinition('type', $definition);
    }

    public function testParseDefinitionFieldClassException(): void
    {
        $this->expectException(InvalidFieldClassException::class);
        $this->expectExceptionMessage('Class Sarue\Orm\Tests\Unit\Field\InvalidClassField has the same options both in SCHEMA_OPTIONS and PROPERTY_OPTIONS: "option", "option2".');

        InvalidClassField::parseDefinition('type', []);
    }

    public function testProperties(): void
    {
        $field = new TestableFieldBase(
            'field_name',
            ['requiredSchema' => 123],
            ['requiredProperty' => 123],
            [],
            true,
        );

        $this->assertEquals('field_name', $field->getFieldName());
        $this->assertEquals(['requiredSchema' => 123], $field->getSchema());
        $this->assertEquals(['requiredProperty' => 123], $field->getProperties());
        $this->assertEmpty($field->getAdditionalDefinition());
        $this->assertTrue($field->isRequired());
    }
}

class TestableFieldBase extends FieldBase
{
    protected const array SCHEMA_OPTIONS = [
        'requiredSchema' => ['required' => true],
        'optionalSchema' => [],
    ];
    protected const array PROPERTY_OPTIONS = [
        'requiredProperty' => ['required' => true],
        'optionalProperty' => [],
    ];

    protected static function validateDefinition(array $rawDefinition, array $schema, array $properties, array $additionalDefinition, bool $required): void
    {
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
