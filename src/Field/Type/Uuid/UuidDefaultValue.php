<?php

namespace Sarue\Orm\Field\Type\Uuid;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\DefaultExpression;

class UuidDefaultValue implements DefaultExpression
{
    public function toSQL(AbstractPlatform $platform): string
    {
        return 'gen_random_uuid()';
    }
}
