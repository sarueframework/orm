<?php

namespace Sarue\Orm\Field\Type\Text;

use Doctrine\DBAL\Schema\Column;
use Sarue\Orm\Field\Type\FieldTypeBase;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;

class TextField extends FieldTypeBase
{
    protected int $value;

    public function getConditionType(): string
    {
        return TextConditionInterface::class;
    }

    public function getSchema(): array
    {
        return [
            Column::editor()
                ->setUnquotedName($this->fieldName)
                ->setTypeName('string')
                ->setNotNull($this->required)
                ->create(),
        ];
    }

    public function validateDefinition(): void
    {
    }
}
