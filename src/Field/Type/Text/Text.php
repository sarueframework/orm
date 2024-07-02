<?php

namespace Sarue\Orm\Field\Type\Text;

use Sarue\Orm\Field\FieldBase;

class Text extends FieldBase
{
    public function sqlColumnCode(): string
    {
        return $this->getFieldName() . ' TEXT';
    }
}
