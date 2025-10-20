<?php

namespace Sarue\Orm\Schema;

interface EntityDiscoveryCacheInterface {
    /**
     * @return \Sarue\Orm\Schema\EntityDefinition[]
     */
    public function getCachedEntityDefinitions(): array;
}
