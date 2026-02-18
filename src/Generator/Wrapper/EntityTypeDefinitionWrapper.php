<?php

namespace Sarue\Orm\Generator\Wrapper;

use Sarue\Orm\Entity\EntityInterface;
use Sarue\Orm\Entity\Type\EntityType;

/**
 * @internal
 */
class EntityTypeDefinitionWrapper
{
    /**
     * @param \ReflectionClass<EntityInterface>     $reflectionClass
     * @param array<string, FieldDefinitionWrapper> $fieldDefinitionWrappers
     */
    public function __construct(
        public readonly EntityType $entityTypeDefinition,
        public readonly \ReflectionClass $reflectionClass,
        public readonly array $fieldDefinitionWrappers,
    ) {
    }
}
