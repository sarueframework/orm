<?php

namespace Sarue\Orm\Field;

use Sarue\Orm\Exception\InvalidDefinitionException;

abstract class FieldBase implements FieldInterface
{
    protected const array SCHEMA_DEFINITION_OPTIONS = [];
    protected const array REQUIRED_SCHEMA_DEFINITION_OPTIONS = [];
    protected const array REQUIRED_ADDITIONAL_DEFINITION_OPTIONS = [];

    public static function createFromDefinition(string $fieldName, array $definition): static
    {
        [$schemaDefinition, $additionalDefinition, $required] = static::parseDefinition($definition);

        return static::createFromSchemaStorage($fieldName, $schemaDefinition, $additionalDefinition, $required);
    }

    public static function createFromSchemaStorage(
        string $fieldName,
        array $schemaDefinition,
        array $additionalDefinition,
        bool $required,
    ): static {
        return new static(
            $fieldName,
            $schemaDefinition,
            $additionalDefinition,
            $required,
        );
    }

    public function __construct(
        protected string $fieldName,
        protected array $schemaDefinition,
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

    protected static function parseDefinition($rawDefinition): array
    {
        $required = static::parseRequiredFromDefinition($rawDefinition);
        ksort($rawDefinition);

        $additionalDefinition = $rawDefinition;
        $schemaDefinition = [];
        foreach ($additionalDefinition as $key => $value) {
            if (in_array($key, static::SCHEMA_DEFINITION_OPTIONS, true)) {
                $schemaDefinition[$key] = $additionalDefinition[$key];

                // The additional definition is the raw definition MINUS the schema definition.
                unset($additionalDefinition[$key]);
            }
        }

        static::validateSchemaDefinition($schemaDefinition, $rawDefinition);
        static::validateAdditionalDefinition($additionalDefinition, $rawDefinition);

        return [$schemaDefinition, $additionalDefinition, $required];
    }

    protected static function parseRequiredFromDefinition(array &$rawDefinition): bool
    {
        $required = $rawDefinition['required'] ?? false;
        if (!is_bool($required)) {
            throw new InvalidDefinitionException('Option "required" must be boolean.');
        }
        unset($rawDefinition['required']);

        return $required;
    }

    protected static function validateSchemaDefinition(array &$schemaDefinition, array $rawDefinition): void
    {
        $missingRequiredOptions = array_diff(array_keys($schemaDefinition), static::REQUIRED_SCHEMA_DEFINITION_OPTIONS);
        if (!empty($missingRequiredOptions)) {
            throw new InvalidDefinitionException('Missing required schema options: ' . implode(', ', $missingRequiredOptions));
        }
    }

    protected static function validateAdditionalDefinition(array &$additionalDefinition, array $rawDefinition): void
    {
        $missingRequiredOptions = array_diff(array_keys($additionalDefinition), static::REQUIRED_ADDITIONAL_DEFINITION_OPTIONS);
        if (!empty($missingRequiredOptions)) {
            throw new InvalidDefinitionException('Missing required additional options: ' . implode(', ', $missingRequiredOptions));
        }
    }
}
