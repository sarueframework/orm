<?php

namespace Sarue\Orm\Field;

use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Field\FieldType\Text;
use Sarue\Orm\Validator\StringValidator\SnakeCaseValidator;

class FieldFactory
{
    /**
     * Creates a field instance from a developer-provided raw definition.
     *
     * The definition WILL be validated.
     *
     * @param string  $fieldName  the name of the field
     * @param mixed[] $definition the definition of the field provided by the developer
     */
    public function createFromDefinition(string $fieldName, array $definition): FieldInterface
    {
        if (empty($definition['type']) || !is_string($definition['type'])) {
            throw new InvalidDefinitionException('The field type must be a string.');
        }

        if (!SnakeCaseValidator::validateStartingWithLetter($fieldName)) {
            throw new InvalidDefinitionException("The field name $fieldName should be in snake_case and start with a letter");
        }

        $class = $this->resolveClassForFieldType($definition['type']);

        if (!is_subclass_of($class, FieldInterface::class)) {
            throw new InvalidDefinitionException("Class $class is not an instance of \Sarue\Orm\Field\FieldInterface.");
        }

        [$schema, $properties, $additionalDefinition, $required] = $class::parseDefinition($definition['type'], $definition);

        /* @var \Sarue\Orm\Field\FieldInterface */
        return new $class($fieldName, $schema, $properties, $additionalDefinition, $required);
    }

    /**
     * Creates a field instance from processed definitions stored in the database.
     *
     * The data from the storage WILL NOT be validated.
     *
     * @param string  $class                the class of the field type
     * @param string  $fieldName            the name of the field, in snake_case
     * @param mixed[] $schema               the schema-related properties, properly cleanedup and processed
     * @param mixed[] $properties           the non-schema-related properties, properly cleanedup and processed
     * @param mixed[] $additionalDefinition anything in the "additional:" part of the definition
     * @param bool    $required             whether the field is required or not
     */
    public function createFromSchemaStorage(
        string $class,
        string $fieldName,
        array $schema,
        array $properties,
        array $additionalDefinition,
        bool $required,
    ): FieldInterface {
        if (!is_subclass_of($class, FieldInterface::class)) {
            throw new InvalidDefinitionException("Class $class is not an instance of \Sarue\Orm\Field\FieldInterface.");
        }

        return new $class($fieldName, $schema, $properties, $additionalDefinition, $required);
    }

    public function resolveClassForFieldType(string $fieldType): string
    {
        // @todo Figure out better way to discover Field Types
        return match ($fieldType) {
            'string' => Text\StringFieldType::class,
            default => throw new InvalidDefinitionException("$fieldType is not a valid field type."),
        };
    }
}
