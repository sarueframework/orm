<?php

namespace Sarue\Orm\Schema;

use Doctrine\DBAL\Schema\Table;

/**
 * Stores the cached definition of an entity, extracted from attributes.
 */
class EntityDefinition
{
    public static function __set_state($properties)
    {
        return new static(...$properties);
    }

    /**
     * @param class-string                               $className
     * @param \Sarue\Orm\Field\Type\FieldTypeInterface[] $fields
     */
    public function __construct(
        public readonly string $name,

        public readonly string $className,

        public readonly array $fields,
    ) {
    }

    public function createTableSchema(): Table
    {
        $tableEditor = Table::editor()
            ->setUnquotedName($this->name);

        foreach ($this->fields as $fieldDefinition) {
            foreach ($fieldDefinition->createSchema() as $column) {
                $tableEditor->addColumn($column);
            }
        }

        return $tableEditor->create();
    }
}
