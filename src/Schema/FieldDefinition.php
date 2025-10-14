<?php

namespace Sarue\Orm\Schema;

/**
 * Stores the cached definition of a field, extracted from attributes.
 */
class FieldDefinition {
    public static function __set_state($properties)
    {
        return new static(...$properties);
    }

    public function __construct(
        /**
         * @var string
         */
        public readonly string $name,

        /**
         * @var class-string
         */
        public readonly string $type,

        /**
         * @var class-string
         */
        public readonly string $conditionType,
    )
    {
    }

    public function validate(): void
    {
        if ($this->name === 'id') {
            throw new \Exception('A property named ID is forbidden.');
        }
    }
}
