<?php

namespace Sarue\Orm\Query\Parameter;

use BcMath\Number;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Sarue\Orm\Query\Parameter\ParameterBase;

class NumberParameter extends ParameterBase
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::INTEGER;

    public function __construct(Number $value)
    {
        $this->value = $value;
    }
}
