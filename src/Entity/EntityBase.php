<?php

namespace Sarue\Orm\Entity;

abstract class EntityBase implements EntityInterface
{
    public static function fromValues(array $values): static
    {
        $entity = new static();

        foreach ($values as $fieldName => $value) {
            $entity->{$fieldName} = $value;
        }

        return $entity;
    }
}
