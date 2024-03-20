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

        if (!SnakeCaseValidator::validate($fieldName)) {
            throw new InvalidDefinitionException("The field name $fieldName should be in snake_case and start with a letter");
        }

        $class = $this->resolveClassForFieldType($definition['type']);

        if (!is_subclass_of($class, FieldInterface::class)) {
            // @todo Create proper exception.
            throw new \Exception("Class $class is not an instance of \Sarue\Orm\Field\FieldInterface.");
        }

        [$schemaDefinition, $additionalDefinition, $required] = $class::parseDefinition($definition);

        /* @var \Sarue\Orm\Field\FieldInterface */
        return new $class($fieldName, $schemaDefinition, $additionalDefinition, $required);
    }

    public function resolveClassForFieldType(string $fieldType): string
    {
        // @todo Figure out better way to discover Field Types
        $fieldClasses = [
            'string' => Text\StringFieldType::class,
        ];

        if (empty($fieldClasses[$fieldType])) {
            // @todo Create custom Exception and list valid field types in the exception message
            throw new \InvalidArgumentException("$fieldType is not a valid field type.");
        }

        return $fieldClasses[$fieldType];
    }
}
