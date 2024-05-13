<?php

namespace Sarue\Orm\Entity;

use Sarue\Orm\Entity\Type\EntityTypeInterface;

class Entity
{
    protected ?string $id;

    public function __construct(
        protected EntityTypeInterface $entityType,
        protected array $values,
    ) {
        $this->id = $values['id'] ?? NULL;
        unset($values['id']);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getEntityType(): EntityTypeInterface
    {
        return $this->entityType;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function __call($name, $arguments)
    {
        if (strpos($name, 'get') === 0) {
            $get = true;
            if (count($arguments) > 0) {
                throw new \Exception('Arguments should not be passed to get*().');
            }
            return $this->values[$this->getFieldNameFromMethodName($name)];
        }
        elseif (strpos($name, 'set') === 0) {
            $get = false;
            if (count($arguments) !== 1) {
                throw new \Exception('set*() should receive exactly 1 method.');
            }
            $this->values[$this->getFieldNameFromMethodName($name)] = $arguments[0];
            return $this;
        }
        else {
            throw new \Exception('Call should only be get and set.');
        }
    }

    protected function getFieldNameFromMethodName(string $name): string
    {
        $fieldName = strtolower(substr(preg_replace('/([A-Z])/', '_$1', substr($name, 3)), 1));
        // Checks that fields exist.
        $this->entityType->getField($fieldName);
        return $fieldName;
    }
}
