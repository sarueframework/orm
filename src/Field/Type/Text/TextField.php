<?php

namespace Sarue\Orm\Field\Type\Text;

use Doctrine\DBAL\Schema\Column;
use Sarue\Orm\Field\Type\FieldTypeBase;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class TextField extends FieldTypeBase
{
    protected const ALLOWED_PROPERTY_TYPES = ['string'];

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
