<?php

namespace Sarue\Orm\Entity\Type;

interface EntityTypeDefinitionRepositoryInterface
{
    /**
     * @return EntityType[]
     */
    public function getCachedEntityDefinitions(): array;
}
