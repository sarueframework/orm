<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    /**
     * Creates an entity type instance from a developer-provided raw definition.
     *
     * The definition WILL be validated.
     *
     * @param string  $fieldName  the name of the field
     * @param mixed[] $definition the definition of the field provided by the developer
     */
    public static function createFromDefinition(string $fieldName, array $definition): static;

    /**
     * Creates a field instance from a developer-provided raw definition.
     *
     * The data from the storage WILL NOT be validated.
     *
     * @param mixed[] $schemaDefinition     the schema-related options from the storage
     * @param mixed[] $additionalDefinition the non-schema-related options from the storage
     * @param bool    $required             the data from the storage
     */
    public static function createFromSchemaStorage(
        string $fieldName,
        array $schemaDefinition,
        array $additionalDefinition,
        bool $required,
    ): static;

    /**
     * Builder.
     *
     * @param mixed[] $schemaDefinition     the schema-related options from the storage
     * @param mixed[] $additionalDefinition the non-schema-related options from the storage
     * @param bool    $required             the data from the storage
     */
    public function __construct(
        string $fieldName,
        array $schemaDefinition,
        array $additionalDefinition,
        bool $required,
    );

    public function getFieldName(): string;

    /**
     * @return mixed[]
     */
    public function getSchemaDefinition(): array;

    /**
     * @return mixed[]
     */
    public function getAdditionalDefinition(): array;

    public function isRequired(): bool;

    // @todo: add methods schema(), validate(), prepareToStorage().
}
