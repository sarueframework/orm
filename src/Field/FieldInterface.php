<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    /**
     * @param mixed[] $schema               the schema-related properties, properly cleanedup and processed
     * @param mixed[] $properties           the non-schema-related properties, properly cleanedup and processed
     * @param bool    $required             whether the field is required or not
     */
    public function __construct(
        string $fieldName,
        array $schema,
        array $properties,
        bool $required,
    );

    /**
     * Gets the field name.
     *
     * @return string the field name, that will be in snake_case
     */
    public function getFieldName(): string;

    /**
     * @return mixed[] the options in the definition that affect the database structure
     */
    public function getSchema(): array;

    /**
     * @return mixed[] the options in the definition that DO NOT affect the database structure
     */
    public function getProperties(): array;

    /**
     * @return bool whether the field is required or not
     */
    public function isRequired(): bool;

    // @todo: add methods schema(), validate(), prepareToStorage().
}
