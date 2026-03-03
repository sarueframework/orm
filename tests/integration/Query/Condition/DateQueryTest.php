<?php

namespace Sarue\Orm\Tests\Integration\Query\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\DateTime\DateTimeConditionInterface;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DateTimeDummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isAfter;
use function Sarue\Orm\Query\Condition\isAfterOrExactly;
use function Sarue\Orm\Query\Condition\isBefore;
use function Sarue\Orm\Query\Condition\isBeforeOrExactly;
use function Sarue\Orm\Query\Condition\isExactyDateTime;
use function Sarue\Orm\Query\Condition\isNotDateTime;
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
        $date = new \DateTime('2020-02-01T00:00:00');
        $midday = new \DateTime('2020-02-01T12:00:00');

        return [
            [isBefore($date), ['2020-01-01', '2020-01-05']],
            [isBefore($midday), ['2020-01-01', '2020-01-05']],
            [isBeforeOrExactly($date), ['2020-01-01', '2020-01-05', '2020-02-01']],
            [isBeforeOrExactly($midday), ['2020-01-01', '2020-01-05', '2020-02-01']],
            [isExactyDateTime($date), ['2020-02-01']],
            [isExactyDateTime($midday), ['2020-02-01']],
            [isNotDateTime($date), ['2020-01-01', '2020-01-05', '2020-02-02']],
            [isNotDateTime($midday), ['2020-01-01', '2020-01-05', '2020-02-02']],
            [isAfter($date), ['2020-02-02']],
            [isAfter($midday), ['2020-02-02']],
            [isAfterOrExactly($date), ['2020-02-01', '2020-02-02']],
            [isAfterOrExactly($midday), ['2020-02-01', '2020-02-02']],
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
