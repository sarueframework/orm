<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
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
