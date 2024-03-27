<?php

namespace Sarue\Orm\Tests\Unit\Field;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Exception\InvalidDefinitionException;
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
            'requiredSchemaOption' => 123,
            'optionalSchemaOption' => 456,
            'requiredAdditionalOption' => 789,
            'optionalAdditionalOption' => 987,
        ];

        if (!is_null($requiredDefinition)) {
            $definition['required'] = $requiredDefinition;
        }

        [$schemaDefinition, $additionalDefinition, $required] = TestableFieldBase::parseDefinition($definition);
        $this->assertEquals([
            'optionalSchemaOption' => 456,
            'requiredSchemaOption' => 123,
        ], $schemaDefinition);
        $this->assertEquals([
            'optionalAdditionalOption' => 987,
            'requiredAdditionalOption' => 789,
            'type' => 'string',
        ], $additionalDefinition);
        $this->assertEquals((bool) $requiredDefinition, $required);
    }

    public static function dataProviderTestParseDefinitionException(): array
    {
        return [
            [
                [
                    'optionalSchemaOption' => 456,
                ],
                InvalidDefinitionException::class,
                'Missing required schema options: requiredSchemaOption',
            ],
            [
                [
                    'requiredSchemaOption' => 123,
                ],
                InvalidDefinitionException::class,
                'Missing required additional options: requiredAdditionalOption',
            ],
            [
                [
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

        TestableFieldBase::parseDefinition($definition);
    }

    public function testProperties(): void
    {
        $field = new TestableFieldBase(
            'field_name',
            ['requiredSchemaOption' => 123],
            [],
            [],
            true,
        );

        $this->assertEquals('field_name', $field->getFieldName());
        $this->assertEquals(['requiredSchemaOption' => 123], $field->getSchemaDefinition());
        $this->assertEmpty($field->getAdditionalDefinition());
        $this->assertTrue($field->isRequired());
    }
}

class TestableFieldBase extends FieldBase
{
    protected const array SCHEMA_DEFINITION_OPTIONS = ['requiredSchemaOption', 'optionalSchemaOption'];
    protected const array REQUIRED_SCHEMA_DEFINITION_OPTIONS = ['requiredSchemaOption'];
    protected const array REQUIRED_ADDITIONAL_DEFINITION_OPTIONS = ['requiredAdditionalOption'];

    protected static function validateDefinition(array $rawDefinition, array $schema, array $properties, array $additionalDefinition, bool $required)
    {
        // do nothing
    }
}
