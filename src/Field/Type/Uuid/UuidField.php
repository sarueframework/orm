<?php

namespace Sarue\Orm\Field\Type\Uuid;

use Doctrine\DBAL\Schema\ColumnEditor;
use Sarue\Orm\Field\Type\ScalarFieldTypeBase;
use Sarue\Orm\Query\Condition\Numeric\UuidConditionInterface;

class UuidField extends ScalarFieldTypeBase
{
    public const string COLUMN_TYPE = 'guid';

    public const array ALLOWED_PROPERTY_TYPES = ['string'];

    public function __construct(
        public readonly bool $generateIdByDefault = FALSE,
    )
    {
    }

    public function getConditionType(): string
    {
        return UuidConditionInterface::class;
    }

    public function fromDatabaseValue(mixed $databaseValue): mixed
    {
        return NULL;
    }

    public function validateDefinition(): void
    {
    }

    protected function getColumnEditor(): ColumnEditor
    {
        $columnEditor = parent::getColumnEditor();

        if ($this->generateIdByDefault) {
            $columnEditor->setDefaultValue(new UuidDefaultValue());
        }

        return $columnEditor;
    }
}
