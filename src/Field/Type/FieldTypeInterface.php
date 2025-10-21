<?php

namespace Sarue\Orm\Field\Type;

interface FieldTypeInterface
{
    public string $fieldName { set; }

    public string $propertyType { set; }

    public bool $required { set; }

    public function getConditionType(): string;

    /**
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    public function getSchema(): array;

    public function validateDefinition(): void;
}
