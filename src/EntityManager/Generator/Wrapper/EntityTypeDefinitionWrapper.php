<?php

namespace Sarue\Orm\EntityManager\Generator\Wrapper;

use Sarue\Orm\Entity\Type\EntityType;

/**
 * @internal
 */
class EntityTypeDefinitionWrapper
{
    /**
     * @param array<string, FieldDefinitionWrapper> $fieldDefinitionWrappers
     */
    public function __construct(
        public readonly EntityType $entityTypeDefinition,
        public readonly \ReflectionClass $reflectionClass,
        public readonly array $fieldDefinitionWrappers,
    )
    {
    }
}
