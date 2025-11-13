<?php

namespace Sarue\Orm\Entity;

interface EntityInterface
{
    public static function fromValues(array $values): static;
}
