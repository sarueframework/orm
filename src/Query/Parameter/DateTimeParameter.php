<?php

namespace Sarue\Orm\Query\Parameter;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;

class DateTimeParameter extends AbstractParameter
{
    protected const ParameterType|ArrayParameterType PARAMETER_TYPE = ParameterType::STRING;

    public function __construct(\DateTime $value)
    {
        $value->setTimezone(new \DateTimeZone('UTC'));
        parent::__construct($value->format('Y-m-d'));
    }
}
