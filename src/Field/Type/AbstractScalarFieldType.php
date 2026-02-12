<?php

namespace Sarue\Orm\Field\Type;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ColumnEditor;

abstract class AbstractScalarFieldType extends AbstractFieldType
{
    public const string COLUMN_TYPE = '';

    public function createSchema(): array
    {
        return [$this->getColumnEditor()->create()];
    }

    protected function getColumnEditor(): ColumnEditor
    {
        return Column::editor()
            ->setUnquotedName($this->fieldName)
            ->setTypeName(static::COLUMN_TYPE)
            ->setNotNull($this->required);
    }
}
