<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    /**
     * Parses a definition, separating schema from additional definitions.
     *
     * @param mixed[] $rawDefinition the definition of the field provided by the developer
     *
     * @return array{0: mixed[], 1: mixed[], 2: bool}
     */
    public static function parseDefinition(array $rawDefinition): array;

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
