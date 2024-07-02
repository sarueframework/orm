<?php

namespace Sarue\Orm\Field\Type\Time;

use Sarue\Orm\Field\FieldBase;

class Date extends FieldBase
{
    public function sqlColumnCode(): string
    {
        return $this->getFieldName() . ' DATE';
    }
}
