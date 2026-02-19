<?php

namespace Sarue\Orm\Entity\Type;

use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Table;
use Sarue\Orm\Entity\RevisionableEntityInterface;
use Sarue\Orm\Field\Type\FieldTypeInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class EntityType
{
    /**
     * @var non-empty-string
     */
    protected string $name;

    /**
     * @var class-string
     */
    protected string $className;

    /**
     * @var array<string, FieldTypeInterface>
     */
    protected array $fields;

    /**
     * @param non-empty-string     $name
     * @param class-string         $className
     * @param FieldTypeInterface[] $fields
     */
    public static function fromValues(string $name, string $className, array $fields): self
    {
        $entityType = new self();
        $entityType->name = $name;
        $entityType->className = $className;
        $entityType->fields = $fields;

        return $entityType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return array<string, FieldTypeInterface>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return non-empty-string
     */
    public function getTableName(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function getRevisionTableName(): string
    {
        return $this->name.'__revision';
    }

    /**
     * @return Table[]
     */
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
        return is_subclass_of($this->className, RevisionableEntityInterface::class);
    }
}
