<?php

namespace Sarue\Orm\Entity\Type;

use Sarue\Orm\Field\FieldInterface;

interface EntityTypeInterface
{
    public function getName(): string;

    public function getField(string $fieldName): FieldInterface;

    /**
     * Gets the list of all fields defined in the entity.
     *
     * @return FieldInterface[] $fields
     */
    public function getFields(): array;

    /**
     * Exports entity type to an array to be stored in the processed definition storage.
     *
     * @return array{fields: array<array{class: string, schema: mixed[], additional: mixed[], required: bool}>}
     */
    public function toStorage(): array;
}
