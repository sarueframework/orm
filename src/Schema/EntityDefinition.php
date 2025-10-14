<?php

namespace Sarue\Orm\Schema;

/**
 * Stores the cached definition of an entity, extracted from attributes.
 */
class EntityDefinition {
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
        public readonly string $className,

        /**
         * @var \Sarue\Orm\Schema\FieldDefinition[]
         */
        public readonly array $fields,

        public string $some,
    )
    {
    }
}
