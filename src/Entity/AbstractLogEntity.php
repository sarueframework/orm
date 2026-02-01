<?php

namespace Sarue\Orm\Entity;

/**
 * Base class for Log Entities.
 *
 * Log entities are entities that can be inserted but not deleted or updated.
 */
abstract class AbstractLogEntity extends AbstractBaseEntity
{
    final public static function isRevisionable(): bool
    {
        return false;
    }

    final public function mayDelete(): bool
    {
        return false;
    }
}
