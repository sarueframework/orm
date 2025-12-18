<?php

namespace Sarue\Orm\Entity;

interface EntityInterface
{
    public static function fromDatabaseValues(array $values): static;
}
