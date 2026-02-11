<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isGreaterThan;

class EntityQueryTest extends IntegrationTestCase
{
    public function testNumericQueries(): void
    {
        $entity1 = new DummyEntity();
        $entity1->name = 'Ludwig van Beethoven';
        $entity1->yearOfBirth = 1770;
        $entity1->save();

        $entity2 = new DummyEntity();
        $entity2->name = 'Johann Sebastian Bach';
        $entity2->yearOfBirth = 1685;
        $entity2->save();

        $entity3 = new DummyEntity();
        $entity3->name = 'Gustav Mahler';
        $entity3->yearOfBirth = 1860;
        $entity3->save();

        $entity4 = new DummyEntity();
        $entity4->name = 'Felix Mendelssohn';
        $entity4->yearOfBirth = 1809;
        $entity4->save();

        $entity5 = new DummyEntity();
        $entity5->name = 'Dmitri Shostakovich';
        $entity5->yearOfBirth = 1906;
        $entity5->save();

        $composers20thCentury = $this->ormManager
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->where(
                yearOfBirth: isGreaterThan(1900),
            )
            ->loadAll()
        ;
        $this->assertCount(1, $composers20thCentury);
        $shostakovich = reset($composers20thCentury);
        $this->assertEquals('Dmitri Shostakovich', $shostakovich->name);
        $this->assertEquals(1906, $shostakovich->yearOfBirth);
    }
}
