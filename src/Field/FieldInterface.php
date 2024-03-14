<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    public static function createFromDefinition(string $fieldName, array $definition);

    public static function createFromSchemaStorage(
        string $fieldName,
        array $schemaDefinition,
        array $additionalDefinition,
        bool $required,
    );

    public function getFieldName(): string;

    public function getSchemaDefinition(): array;

    public function getAdditionalDefinition(): array;

    public function isRequired(): bool;

    public function getHash(): string;

    // @todo: add methods schema(), validate(), prepareToStorage().
}