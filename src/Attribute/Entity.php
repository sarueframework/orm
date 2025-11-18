<?php

namespace Sarue\Orm\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Entity
{
    public function __construct(string $some)
    {
    }
}
