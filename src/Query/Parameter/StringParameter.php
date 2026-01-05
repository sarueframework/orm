<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class StringParameter extends ParameterBase
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::STRING;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
