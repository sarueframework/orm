<?php

namespace Sarue\Orm\Entity;

interface EntityInterface
{
    public static function fromDatabaseValues(array $values): static;
    public function isNew(): bool;
}
