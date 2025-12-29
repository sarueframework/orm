<?php

namespace Sarue\Orm\Schema;

interface EntityDiscoveryCacheInterface
{
    /**
     * @return EntityType[]
     */
    public function getCachedEntityDefinitions(): array;
}
