<?php

namespace Sarue\Orm\Field\Type\Uuid;

use Doctrine\DBAL\Schema\ColumnEditor;
use Sarue\Orm\Field\Type\ScalarFieldTypeBase;
use Sarue\Orm\Query\Condition\Numeric\UuidConditionInterface;
use Sarue\Orm\Query\Parameter\NullParameter;
use Sarue\Orm\Query\Parameter\StringParameter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UuidField extends ScalarFieldTypeBase
{
    public const string COLUMN_TYPE = 'guid';

    public const array ALLOWED_PROPERTY_TYPES = ['string'];

    public function __construct(
        public readonly bool $generateIdByDefault = false,
    ) {
    }

    public function getConditionType(): string
    {
        return UuidConditionInterface::class;
    }

    public function fromDatabaseValue(mixed $databaseValue): mixed
    {
        return $databaseValue;
    }

    public function toDatabaseValues(mixed $fieldValue): array
    {
        // @todo Validate if value is string or ?string & not required.
        return [
            $this->fieldName => $fieldValue ? new StringParameter($fieldValue) : new NullParameter(),
        ];
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
