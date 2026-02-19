<?php

namespace Sarue\Orm\Query\Parameter;

use BcMath\Number;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class NumberParameter extends AbstractParameter
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::STRING;

    public function __construct(Number $value)
    {
        parent::__construct($value);
    }
}
