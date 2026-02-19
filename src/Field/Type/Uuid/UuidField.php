<?php

namespace Sarue\Orm\Field\Type\Uuid;

use Doctrine\DBAL\Schema\ColumnEditor;
use Sarue\Orm\Field\Type\AbstractScalarFieldType;
use Sarue\Orm\Query\Condition\Numeric\UuidConditionInterface;
use Sarue\Orm\Query\Parameter\NullParameter;
use Sarue\Orm\Query\Parameter\TextParameter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class UuidField extends AbstractScalarFieldType
{
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
        if (empty($fieldValue)) {
            if ($this->isRequired()) {
                throw new \Exception(sprintf('Field %s must not be empty.', $this->fieldName));
            }

            $parameter = new NullParameter();
        } elseif (is_string($fieldValue)) {
            $parameter = new TextParameter($fieldValue);
        } else {
            throw new \Exception(sprintf('Field %s must be a string.', $this->fieldName));
        }

        return [$this->fieldName => $parameter];
    }

    public function validateDefinition(): void
    {
    }

    protected function getColumnType(): string
    {
        return 'guid';
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
