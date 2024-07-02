<?php

namespace Sarue\Orm\Field\Type\Numeric;

use Sarue\Orm\Field\FieldBase;

class Integer extends FieldBase
{
    public function sqlColumnCode(): string
    {
        return $this->getFieldName() . ' INTEGER';
    }
}
