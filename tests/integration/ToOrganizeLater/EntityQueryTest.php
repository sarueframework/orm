<?php

namespace Sarue\Orm\Tests\Integration\ToOrganizeLater;

use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isGreaterThan;
use function Sarue\Orm\Query\Condition\isLessThanOrEqualTo;
use function Sarue\Orm\Query\Sort\asc;

class EntityQueryTest extends IntegrationTestCase
{
    public function testAllNumericConditions()
    {
        $years = [
            1900,
            1901,
            1902,
            1903,
            1904,
        ];
        foreach ($years as $year) {
            $entity = new DummyEntity();
            $entity->name = 'AA';
            $entity->yearOfBirth = $year;
            $entity->save();
        }

        // Not using DataProvider so that the test runs more quickly with just
        // one batch of insertions.
        $testCases = [
            ['isGreaterThan', [1903, 1904]],
            ['isGreaterThanOrEqualTo', [1902, 1903, 1904]],
            ['isLessThan', [1900, 1901]],
            ['isLessThanOrEqualTo', [1900, 1901, 1902]],
            ['isEqualTo', [1902]],
            ['isNotEqualTo', [1900, 1901, 1903, 1904]],
        ];

        foreach ($testCases as $testCase) {
            [$function, $expected] = $testCase;

            $entities = $this->ormManager
                ->getQueryFactory()
                ->getDummyEntityQuery()
                ->where(
                    yearOfBirth: ('Sarue\\Orm\\Query\\Condition\\'.$function)(1902),
                )
                ->orderBy(yearOfBirth: asc())
                ->loadAll()
            ;
            $this->assertEquals(
                $expected,
                array_map(
                    fn (DummyEntity $entity) => $entity->yearOfBirth,
                    $entities,
                ),
            );
        }
    }

    public function testCombinedNumericConditions(): void
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

        $composers19thCentury = $this->ormManager
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->where(
                yearOfBirth: isGreaterThan(1800),
            )
            ->and(
                yearOfBirth: isLessThanOrEqualTo(1900),
            )
            ->orderBy(
                name: asc(),
            )
            ->loadAll()
        ;

        $composers20thCentury = $this->ormManager
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->where(
                yearOfBirth: isGreaterThan(1900),
            )
            ->loadAll()
        ;

        $this->assertCount(2, $composers19thCentury);
        $mendelssohn = reset($composers19thCentury);
        $mahler = next($composers19thCentury);
        $this->assertEquals('Felix Mendelssohn', $mendelssohn->name);
        $this->assertEquals(1809, $mendelssohn->yearOfBirth);
        $this->assertEquals('Gustav Mahler', $mahler->name);
        $this->assertEquals(1860, $mahler->yearOfBirth);

        $this->assertCount(1, $composers20thCentury);
        $shostakovich = reset($composers20thCentury);
        $this->assertEquals('Dmitri Shostakovich', $shostakovich->name);
        $this->assertEquals(1906, $shostakovich->yearOfBirth);
    }
}
