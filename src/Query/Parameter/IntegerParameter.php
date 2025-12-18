<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Sarue\Orm\Query\Parameter\ParameterBase;

class IntegerParameter extends ParameterBase
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::INTEGER;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
