<?php

namespace Sarue\Orm\Entity\Type;

use Sarue\Orm\Field\FieldInterface;

interface EntityTypeInterface
{
    /**
     * Creates an entity type instance from a developer-provided raw definition.
     *
     * The definition WILL be validated.
     *
     * @param string  $entityTypeName the name of the entity type
     * @param mixed[] $definition     the definition of the entity type provided by the developer
     */
    public static function createFromDefinition(string $entityTypeName, array $definition): static;

    /**
     * Creates an entity type instance from a developer-provided raw definition.
     *
     * The data from the storage WILL NOT be validated.
     *
     * @param string                                                                            $entityTypeName    the name of the entity type
     * @param array<array{class: string, schema: mixed[], additional: mixed[], required: bool}> $fieldsFromStorage the data from the storage
     */
    public static function createFromSchemaStorage(string $entityTypeName, array $fieldsFromStorage): static;

    /**
     * Builder.
     *
     * @param string           $entityTypeName the name of the entity type
     * @param FieldInterface[] $fields         the list of fields, already instantiated
     */
    public function __construct(
        string $entityTypeName,
        array $fields,
    );

    public function getName(): string;

    public function getField(string $fieldName): FieldInterface;

    /**
     * Gets the list of all fields defined in the entity.
     *
     * @return FieldInterface[] $fields
     */
    public function getFields(): array;
}
