<?php

namespace Sarue\Orm\Generator\Wrapper;

use Sarue\Orm\Field\Type\FieldTypeInterface;

/**
 * @internal
 */
class FieldDefinitionWrapper
{
    /**
     * @param mixed[] $arguments
     */
    public function __construct(
        public readonly FieldTypeInterface $fieldDefinition,
        public readonly ?string $propertyType,
        public readonly array $arguments = [],
    ) {
    }
}
