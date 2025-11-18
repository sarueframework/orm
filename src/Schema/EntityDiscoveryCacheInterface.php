<?php

namespace Sarue\Orm\Schema;

interface EntityDiscoveryCacheInterface
{
    /**
     * @return EntityDefinition[]
     */
    public function getCachedEntityDefinitions(): array;
}
