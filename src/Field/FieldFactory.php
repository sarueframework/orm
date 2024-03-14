<?php

namespace Sarue\Orm\Field;

use Sarue\Orm\Field\FieldType\Text;

class FieldFactory
{
    public function createFromDefinition(string $fieldType, string $fieldName, array $definition): FieldInterface
    {
        $class = $this->resolveClassForFieldType($fieldType);

        /** @var \Sarue\Orm\Field\FieldInterface */
        return $class::createFromDefinition($fieldName, $definition);
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
