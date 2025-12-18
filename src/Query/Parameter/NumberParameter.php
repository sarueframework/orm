<?php

namespace Sarue\Orm\Query\Parameter;

use BcMath\Number;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class NumberParameter extends ParameterBase
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::INTEGER;

    public function __construct(Number $value)
    {
        $this->value = $value;
    }
}
