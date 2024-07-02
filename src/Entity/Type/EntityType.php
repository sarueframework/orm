<?php

namespace Sarue\Orm\Entity\Type;

use Sarue\Orm\Exception\NonExistingFieldException;
use Sarue\Orm\Field\FieldInterface;

class EntityType implements EntityTypeInterface
{
    /** @var \Sarue\Orm\Field\FieldInterface[] */
    protected $fields;

    /**
     * @param \Sarue\Orm\Field\FieldInterface[]
     */
    public function __construct(
        protected string $entityTypeName,
        array $fields,
    ) {
        foreach ($fields as $field) {
            $this->fields[$field->getFieldName()] = $field;
        }
    }

    public function getName(): string
    {
        return $this->entityTypeName;
    }

    public function getTableName(): string
    {
        return $this->getName();
    }

    public function getRevisionTableName(): string
    {
        return $this->getTableName() . '__revision';
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
                    'required' => $field->isRequired(),
                ],
                $this->getFields(),
            ),
        ];
    }
}
