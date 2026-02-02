<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyLogEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

class EntityDefinitionTest extends IntegrationTestCase
{
    public function testEntityDefinition(): void
    {
        $entityDefinitions = $this->ormManager->getEntityTypeDefinitions();
        $this->assertCount(1, $entityDefinitions);
        $this->assertArrayHasKey(DummyLogEntity::class, $entityDefinitions);
    }
}
