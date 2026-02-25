<?php

namespace Sarue\Orm\Tests\Integration\Query\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\DateTime\DateTimeConditionInterface;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DateTimeDummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isBefore;
use function Sarue\Orm\Query\Condition\isBeforeOrExactly;
use function Sarue\Orm\Query\Sort\asc;

class DateQueryTest extends IntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $dates = [
            '2020-01-01',
            '2020-01-05',
            '2020-02-01',
            '2020-02-02',
        ];
        foreach ($dates as $date) {
            $entity = new DateTimeDummyEntity();
            $entity->date = new \DateTime($date);
            $entity->save();
        }
    }

    public static function dataProviderForTestDateConditions(): array
    {
        return [
            [isBefore(new \DateTime('2020-02-01T00:00:00')), ['2020-01-01', '2020-01-05']],
            [isBeforeOrExactly(new \DateTime('2020-02-01T00:00:00')), ['2020-01-01', '2020-01-05', '2020-02-01']],
        ];
    }

    #[DataProvider('dataProviderForTestDateConditions')]
    public function testDateConditions(DateTimeConditionInterface $condition, array $expected): void
    {
        $entities = OrmManager::getInstance()
            ->getQueryFactory()
            ->getDateTimeDummyEntityQuery()
            ->where(
                date: $condition,
            )
            ->orderBy(date: asc())
            ->loadAll()
        ;
        $this->assertEquals(
            $expected,
            array_map(
                fn (DateTimeDummyEntity $entity) => $entity->date->format('Y-m-d'),
                $entities,
            ),
        );
    }
}
