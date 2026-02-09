<?php

namespace Sarue\Orm\Entity\Type;

use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
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

    public function getTableName(): string
    {
        return $this->name;
    }

    public function getRevisionTableName(): string
    {
        return $this->name.'__revision';
    }

    public function createTableSchemas(): array
    {
        $tableEditor = Table::editor()
            ->setUnquotedName($this->getTableName());

        if ($this->isRevisionable()) {
            $revisionTableEditor = Table::editor()
                ->setUnquotedName($this->getRevisionTableName());
        }

        foreach ($this->fields as $fieldDefinition) {
            foreach ($fieldDefinition->createSchema() as $column) {
                $tableEditor->addColumn($column);

                if ($this->isRevisionable()) {
                    $revisionTableEditor->addColumn($column);
                }
            }
        }

        $tableEditor->addPrimaryKeyConstraint(
            PrimaryKeyConstraint::editor()
                ->setUnquotedColumnNames('id')
                ->create()
        );

        return $this->isRevisionable() ? [
            $tableEditor->create(),
            $revisionTableEditor->create(),
        ] : [
            $tableEditor->create(),
        ];
    }

    public function isRevisionable(): bool
    {
        return $this->className::isRevisionable();
    }
}
