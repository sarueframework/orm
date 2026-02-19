<?php

namespace Sarue\Orm\Field\Type\Text;

use Sarue\Orm\Field\Type\AbstractScalarFieldType;
use Sarue\Orm\Query\Condition\Text\TextConditionInterface;
use Sarue\Orm\Query\Parameter\NullParameter;
use Sarue\Orm\Query\Parameter\TextParameter;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class TextField extends AbstractScalarFieldType
{
    public const array ALLOWED_PROPERTY_TYPES = ['string'];
    public const string COLUMN_TYPE = 'string';

    public function fromDatabaseValue(mixed $databaseValue): string
    {
        if (!is_string($databaseValue)) {
            throw new \Exception("{$this->fieldName} is expected to be a string.");
        }

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

    public function getConditionType(): string
    {
        return TextConditionInterface::class;
    }

    public function validateDefinition(): void
    {
    }
}
