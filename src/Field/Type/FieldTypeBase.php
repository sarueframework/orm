<?php

namespace Sarue\Orm\Field\Type;

use LogicException;

abstract class FieldTypeBase implements FieldTypeInterface
{
    protected const ALLOWED_PROPERTY_TYPES = [];

    final protected const SET_STATE_PROPERTIES = ['required', 'fieldName', 'propertyType'];

    public final string $fieldName {
        set(string $fieldName) {
            if (isset($this->fieldName)) {
                throw new LogicException('Cannot alter property fieldName.');
            }

            $this->fieldName = $fieldName;
        }
    }

    public final string $propertyType {
        set(?string $propertyType) {
            if (isset($this->propertyType)) {
                throw new LogicException('Cannot alter property propertyType.');
            }

            if (empty($propertyType)) {
                throw new \Exception('The type of a field property must be set.');
            }

            if (str_starts_with($propertyType, '?')) {
                $propertyType = substr($propertyType, 1);
                $this->required = FALSE;
            }
            // The required may be set either here or by _set_state().
            elseif (!isset($this->required)) {
                $this->required = TRUE;
            }

            if (str_contains($propertyType, '&') || str_contains($propertyType, '|')) {
                throw new \Exception('The use of composite properties is not supported.');
            }

            if (empty(static::ALLOWED_PROPERTY_TYPES)) {
                throw new \Exception('The class '.get_class($this).' must set the constant ALLOWED_PROPERTY_TYPES.');
            }

            if (!in_array($propertyType, static::ALLOWED_PROPERTY_TYPES, strict: true)) {
                throw new \Exception('The property type for field ' . $this->fieldName . ' must be one of: "' . implode('", "', static::ALLOWED_PROPERTY_TYPES) . '".');
            }

            $this->propertyType = $propertyType;
        }
    }

    public final bool $required {
        set(bool $required) {
            if (isset($this->required)) {
                throw new LogicException('Cannot alter property required.');
            }

            $this->required = $required;
        }
    }

    public static function __set_state($properties)
    {
        $propertiesToSet = [];
        foreach (static::SET_STATE_PROPERTIES as $propertyName) {
            $propertiesToSet[$propertyName] = $properties[$propertyName];
            unset($properties[$propertyName]);
        }

        $object = new static(...$properties);

        foreach (static::SET_STATE_PROPERTIES as $propertyName) {
            $object->{$propertyName} = $propertiesToSet[$propertyName];
        }

        return $object;
    }
}
