<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class IntegerParameter extends AbstractParameter
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::INTEGER;

    public function __construct(int $value)
    {
        $this->value = $value;
    }
}
