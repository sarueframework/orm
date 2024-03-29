<?php

namespace Sarue\Orm\Entity\Type;

use Sarue\Orm\Exception\NonExistingFieldException;
use Sarue\Orm\Field\FieldInterface;

class EntityType implements EntityTypeInterface
{
    public function __construct(
        protected string $entityTypeName,
        protected array $fields,
    ) {}

    public function getName(): string
    {
        return $this->entityTypeName;
    }

    public function getField(string $fieldName): FieldInterface
    {
        if (!isset($this->fields[$fieldName])) {
            throw new NonExistingFieldException("Field $fieldName does not exist in entity " . $this->getName());
        }

        return $this->fields[$fieldName];
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function toStorage(): array
    {
        return [
            'fields' => array_map(
                fn(FieldInterface $field) => [
                    'class' => get_class($field),
                    'schema' => $field->getSchema(),
                    'properties' => $field->getProperties(),
                    'additional' => $field->getAdditionalDefinition(),
                    'required' => $field->isRequired(),
                ],
                $this->getFields(),
            ),
        ];
    }
}
