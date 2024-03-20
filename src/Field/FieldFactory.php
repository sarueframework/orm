<?php

namespace Sarue\Orm\Field;

use Psr\EventDispatcher\EventDispatcherInterface;
use Sarue\Orm\Event\FieldTypeClassResolutionEvent;
use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Field\FieldType\Text;
use Sarue\Orm\Validator\StringValidator\SnakeCaseValidator;

class FieldFactory
{
    public function __construct(
        protected EventDispatcherInterface $dispatcher,
    ) {}

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

        [$schemaDefinition, $additionalDefinition, $required] = $class::parseDefinition($definition);

        /* @var \Sarue\Orm\Field\FieldInterface */
        return new $class($fieldName, $schemaDefinition, $additionalDefinition, $required);
    }

    /**
     * Creates a field instance from processed definitions stored in the database.
     *
     * The data from the storage WILL NOT be validated.
     *
     * @param mixed[] $schemaDefinition     the schema-related options from the storage
     * @param mixed[] $additionalDefinition the non-schema-related options from the storage
     * @param bool    $required             the data from the storage
     */
    public function createFromSchemaStorage(
        string $class,
        string $fieldName,
        array $schemaDefinition,
        array $additionalDefinition,
        bool $required,
    ): FieldInterface {
        if (!is_subclass_of($class, FieldInterface::class)) {
            // @todo Create proper exception.
            throw new \Exception("Class $class is not an instance of \Sarue\Orm\Field\FieldInterface.");
        }

        return new $class($fieldName, $schemaDefinition, $additionalDefinition, $required);
    }

    public function resolveClassForFieldType(string $fieldType): string
    {
        // @todo Figure out better way to discover Field Types
        $fieldClasses = [
            'string' => Text\StringFieldType::class,
        ];

        // Allows for event listeners to change the class of field type.
        $resolutionEvent = new FieldTypeClassResolutionEvent($fieldType, $fieldClasses[$fieldType] ?? null);
        $this->dispatcher->dispatch($resolutionEvent);

        if (!$resolutionEvent->getClass()) {
            // @todo List valid field types in the exception message
            throw new InvalidDefinitionException("$fieldType is not a valid field type.");
        }

        return $resolutionEvent->getClass();
    }
}
