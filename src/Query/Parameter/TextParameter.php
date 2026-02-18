<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class TextParameter extends AbstractParameter
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::STRING;

    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}
