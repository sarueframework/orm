<?php

namespace Sarue\Orm\Field;

interface FieldInterface
{
    /**
     * Gets the field name.
     *
     * @return string the field name, that will be in snake_case
     */
    public function getFieldName(): string;

    /**
     * @return mixed[] field configuration that do affect the database structure
     */
    public function getSchemaDefinition(): array;

    /**
     * @return mixed[] field configuration that do NOT affect the database structure
     */
    public function getProperties(): array;

    /**
     * @return bool whether the field is required or not
     */
    public function isRequired(): bool;

    /**
     * @return string SQL column creation code
     */
    public function sqlColumnCode(): string;
}
