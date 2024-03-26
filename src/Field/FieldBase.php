<?php

namespace Sarue\Orm\Field;

use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Exception\InvalidFieldClassException;

abstract class FieldBase implements FieldInterface
{
    /**
     * The options in the definition that are schema-related. Example:
     *
     * @code
     *  [
     *      'maxLength' => [
     *          'required' => true,
     *      ],
     *      'cardinality' => [
     *          'default' => 1,
     *      ],
     *      'regex' => [],
     *  ]
     *
     * @endcode
     *
     * For each property you need to define "type" (int, string, array). You may also define "required" and "default".
     *
     * @var array<string, array<string, int|string|mixed[]>>
     */
    protected const array SCHEMA_OPTIONS = [];

    /**
     * The definition of property options, see SCHEMA_DEFINITION for the format.
     *
     * @var array<string, array<string, int|string|mixed[]>>
     */
    protected const array PROPERTY_OPTIONS = [];

    public static function parseDefinition(string $type, array $rawDefinition): array
    {
        // Validates the constants, as they may be developer-provided.
        $intersectOptions = array_intersect(array_keys(static::SCHEMA_OPTIONS), array_keys(static::PROPERTY_OPTIONS));
        if (!empty($intersectOptions)) {
            throw new InvalidFieldClassException('Class ' . static::class . ' has the same options both in SCHEMA_OPTIONS and PROPERTY_OPTIONS: "' . implode('", "', $intersectOptions) . '".');
        }

        // Validates the definition.
        $validOptions = array_merge(
            array_keys(static::SCHEMA_OPTIONS),
            array_keys(static::PROPERTY_OPTIONS),
            ['required', 'additional'],
        );
        $unknownOptions = array_diff(array_keys($rawDefinition), $validOptions);
        if (!empty($unknownOptions)) {
            throw new InvalidDefinitionException('Invalid options found: "' . implode('", "', $unknownOptions) . '". Valid options are: "' . implode('", "', $unknownOptions) . '".');
        }

        // Parses schema and properties.
        $schema = static::parseSchemaAndProperties($rawDefinition, static::SCHEMA_OPTIONS);
        $properties = static::parseSchemaAndProperties($rawDefinition, static::SCHEMA_OPTIONS);

        // Parses out "required:" and "additional:" options.
        $required = static::parseRequiredFromDefinition($rawDefinition);
        $additionalDefinition = static::parseAdditionalFromDefinition($rawDefinition);

        // Append type to the schema.
        $schema['type'] = $type;

        // Sort arrays. Since developers may create their own fields, we're not 100% sure the constants will be tidy.
        ksort($schema);
        ksort($properties);
        ksort($additionalDefinition);

        static::validateDefinition($rawDefinition, $schema, $properties, $additionalDefinition, $required);

        return [$schema, $properties, $additionalDefinition, $required];
    }

    public function __construct(
        protected string $fieldName,
        protected array $schema,
        protected array $properties,
        protected array $additionalDefinition,
        protected bool $required,
    ) {}

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getSchemaDefinition(): array
    {
        return $this->schemaDefinition;
    }

    public function getAdditionalDefinition(): array
    {
        return $this->additionalDefinition;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param mixed[] $rawDefinition the definition of the field provided by the developer
     *
     * @return bool whether the field is required
     */
    protected static function parseRequiredFromDefinition(array $rawDefinition): bool
    {
        $required = $rawDefinition['required'] ?? false;
        if (!is_bool($required)) {
            throw new InvalidDefinitionException('Option "required" must be boolean.');
        }
        unset($rawDefinition['required']);

        return $required;
    }

    /**
     * @param mixed[] $rawDefinition the definition of the field provided by the developer
     *
     * @return mixed[] Additional definition for the field
     */
    protected static function parseAdditionalFromDefinition(array $rawDefinition): array
    {
        $additional = $rawDefinition['additional'] ?? [];
        if (!is_array($additional)) {
            throw new InvalidDefinitionException('Option "additional" must be boolean.');
        }
        unset($rawDefinition['additional']);

        return $additional;
    }

    /**
     * @param mixed[]                                          $rawDefinition the definition of the field provided by
     *                                                                        the developer
     * @param array<string, array<string, int|string|mixed[]>> $options       either static::SCHEMA_OPTIONS or
     *                                                                        static::PROPERTY_OPTIONS
     */
    protected static function parseSchemaAndProperties(array $rawDefinition, array $options): array
    {
        $output = [];
        foreach ($options as $optionName => $option) {
            // Validate if the option is required.
            if (!empty($option['required']) && !isset($rawDefinition[$optionName])) {
                throw new InvalidDefinitionException("Required option \"$optionName\" is missing from definition.");
            }

            $output[$optionName] = $rawDefinition[$optionName] ?? ($option['default'] ?? null);
        }

        return $output;
    }

    /**
     * Validate the definition provided by the developer.
     *
     * @param mixed[] $rawDefinition        the definition of the field provided by
     * @param mixed[] $schema               the schema-related properties, properly cleanedup and processed
     * @param mixed[] $properties           the non-schema-related properties, properly cleanedup and processed
     * @param mixed[] $additionalDefinition anything in the "additional:" part of the definition
     * @param bool    $required             whether the field is required or not
     */
    abstract protected static function validateDefinition(
        array $rawDefinition,
        array $schema,
        array $properties,
        array $additionalDefinition,
        bool $required,
    );
}
