<?php

namespace Sarue\Orm\Entity\Type;

use Sarue\Orm\Exception\InvalidDefinitionException;
use Sarue\Orm\Field\FieldFactory;
use Sarue\Orm\Validator\StringValidator\SnakeCaseValidator;

class EntityTypeFactory
{
    public function __construct(
        protected FieldFactory $fieldFactory
    ) {}

    /**
     * Creates an entity type instance from a developer-provided raw definition.
     *
     * The definition WILL be validated.
     *
     * @param string  $entityTypeName the name of the entity type
     * @param mixed[] $definition     the definition of the entity type provided by the developer
     */
    public function createFromDefinition(string $entityTypeName, array $definition): EntityTypeInterface
    {
        if (!SnakeCaseValidator::validate($entityTypeName)) {
            throw new InvalidDefinitionException("The entity type name $entityTypeName should be in snake_case and start with a letter");
        }

        if (empty($definition['fields']) || !is_array($definition['fields'])) {
            throw new InvalidDefinitionException("Entity $entityTypeName has no no fields in its definition");
        }

        ksort($definition['fields']);

        $fields = [];
        foreach ($definition['fields'] as $fieldName => $fieldDefinition) {
            $fields[$fieldName] = $this->fieldFactory->createFromDefinition($fieldName, $fieldDefinition);
        }

        return new EntityType($entityTypeName, $fields);
    }

    /**
     * Creates an entity type instance from a developer-provided raw definition.
     *
     * The data from the storage WILL NOT be validated.
     *
     * @param string                                                                                           $entityTypeName the name of the entity type
     * @param array{fields: array<array{class: string, schema: mixed[], additional: mixed[], required: bool}>} $storage        the data from the storage
     */
    public function createFromSchemaStorage(string $entityTypeName, array $storage): EntityTypeInterface
    {
        $fields = [];
        foreach ($storage['fields'] as $fieldName => $fieldFromStorage) {
            $fields[$fieldName] = $this->fieldFactory->createFromSchemaStorage(
                $fieldFromStorage['class'],
                $fieldName,
                $fieldFromStorage['schema'],
                $fieldFromStorage['additional'],
                $fieldFromStorage['required'],
            );
        }

        return new EntityType($entityTypeName, $fields);
    }
}
