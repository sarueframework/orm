<?php

namespace Sarue\Orm\Tests\Integration\Schema;

use Sarue\Orm\EntityManager\Generator\ClassGenerator;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query\QueryFactory;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isLargerThan;
use function Sarue\Orm\Query\Condition\startsWith;

class CreateTableTest extends IntegrationTestCase {

    public function testCreateTable(): void {
        $classGenerator = new ClassGenerator(
            __DIR__ . '/../Dummy/Entity',
            __DIR__ . '/../var/sarue-generated',
            entityNamespace: 'Sarue\\Orm\\Tests\\Integration\\Dummy\\Entity\\',
            generatedNamespace: 'Sarue\\Orm\\Tests\\Integration\\Dummy\\Generated\\',
        );
        $classGenerator->generateClasses();

        $queryFactory = new QueryFactory();
        $query = $queryFactory->getDummyEntityQuery();

        $query
            ->age(isLargerThan(18))
            ->name(startsWith('B'));
    }

}
