<?php

namespace Sarue\Orm\EntityManager\Generator\Wrapper;

use Sarue\Orm\Field\Type\FieldTypeInterface;

/**
 * @internal
 */
class FieldDefinitionWrapper
{
    /**
     * @param
     */
    public function __construct(
        public readonly FieldTypeInterface $fieldDefinition,
        public readonly \ReflectionAttribute $attributeReflection,
    )
    {
    }
}
