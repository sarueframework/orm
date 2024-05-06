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

    public function __construct(
        protected string $fieldName,
        protected array $schema,
        protected array $properties,
        protected bool $required,
    ) {}

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getSchema(): array
    {
        return $this->schema;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
