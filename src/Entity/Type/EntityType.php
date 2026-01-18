<?php

namespace Sarue\Orm\Entity\Type;

use Doctrine\DBAL\Schema\Table;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EntityType
{
    public readonly string $name;

    /**
     * @param class-string
     */
    public readonly string $className;

    /**
     * @var \Sarue\Orm\Field\Type\FieldTypeInterface[]
     */
    public readonly array $fields;

    public static function fromValues(string $name, string $className, array $fields)
    {
        $entityType = new static();
        $entityType->name = $name;
        $entityType->className = $className;
        $entityType->fields = $fields;

        return $entityType;
    }

    public function createTableSchemas(): array
    {
        $tableEditor = Table::editor()
            ->setUnquotedName($this->name);
        $revisionTableEditor = Table::editor()
            ->setUnquotedName($this->name.'__revision');

        foreach ($this->fields as $fieldDefinition) {
            foreach ($fieldDefinition->createSchema() as $column) {
                $tableEditor->addColumn($column);
                $revisionTableEditor->addColumn($column);
            }
        }

        return [
            $tableEditor->create(),
            $revisionTableEditor->create(),
        ];
    }
}
