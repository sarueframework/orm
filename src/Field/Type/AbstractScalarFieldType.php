<?php

namespace Sarue\Orm\Field\Type;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnEditor;

abstract class AbstractScalarFieldType extends AbstractFieldType
{
    abstract protected function getColumnType(): string;

    public function createSchema(): array
    {
        return [$this->getColumnEditor()->create()];
    }

    protected function getColumnEditor(): ColumnEditor
    {
        return Column::editor()
            ->setUnquotedName($this->getFieldName())
            ->setTypeName($this->getColumnType())
            ->setNotNull($this->isRequired());
    }
}
