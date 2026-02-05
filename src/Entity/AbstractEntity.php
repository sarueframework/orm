<?php

namespace Sarue\Orm\Entity;

/**
 * Base class for Log Entities.
 *
 * Log entities are entities that can be inserted but not deleted or updated.
 */
abstract class AbstractEntity extends AbstractBaseEntity
{
    final public static function isRevisionable(): bool
    {
        return true;
    }

    protected function mayUpdate(): bool
    {
        // @todo implement permissions.
        return true;
    }

    protected function mayDelete(): bool
    {
        // @todo implement permissions.
        return true;
    }
}
