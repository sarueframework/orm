<?php

namespace Sarue\Orm\Tests\Integration\Query\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\isEqualTo;
use function Sarue\Orm\Query\Condition\isGreaterThan;
use function Sarue\Orm\Query\Condition\isGreaterThanOrEqualTo;
use function Sarue\Orm\Query\Condition\isLessThan;
use function Sarue\Orm\Query\Condition\isLessThanOrEqualTo;
use function Sarue\Orm\Query\Condition\isNotEqualTo;
use function Sarue\Orm\Query\Sort\asc;

class NumericQueryTest extends IntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

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
    }

    public static function dataProviderForTestNumericConditions(): array
    {
        return [
            [isGreaterThan(1902), [1903, 1904]],
            [isGreaterThanOrEqualTo(1902), [1902, 1903, 1904]],
            [isLessThan(1902), [1900, 1901]],
            [isLessThanOrEqualTo(1902), [1900, 1901, 1902]],
            [isEqualTo(1902), [1902]],
            [isNotEqualTo(1902), [1900, 1901, 1903, 1904]],
        ];
    }

    #[DataProvider('dataProviderForTestNumericConditions')]
    public function testNumericConditions(NumericConditionInterface $condition, array $expected): void
    {
        $entities = OrmManager::getInstance()
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->where(
                yearOfBirth: $condition,
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

    public static function dataProviderForTestCombinedAndNumericConditions(): array
    {
        return [
            [isGreaterThan(1900), isGreaterThan(1902), [1903, 1904]],
            [isGreaterThan(1900), isLessThanOrEqualTo(1902), [1901, 1902]],
            [isEqualTo(1902), isEqualTo(1903), []],
            [isEqualTo(1902), isNotEqualTo(1902), []],
            [isEqualTo(1902), isNotEqualTo(1903), [1902]],
            [isGreaterThan(1903), isLessThanOrEqualTo(1902), []],
            [isGreaterThanOrEqualTo(1903), isLessThanOrEqualTo(1905), [1903, 1904]],
        ];
    }

    #[DataProvider('dataProviderForTestCombinedAndNumericConditions')]
    public function testCombinedAndNumericConditions(NumericConditionInterface $condition1, NumericConditionInterface $condition2, $expected): void
    {
        $entities = OrmManager::getInstance()
            ->getQueryFactory()
            ->getDummyEntityQuery()
            ->where(
                yearOfBirth: $condition1,
            )
            ->and(
                yearOfBirth: $condition2,
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

    public static function dataProviderForTestCombinedOrNumericConditions(): array
    {
        return [
            [isGreaterThan(1900), isGreaterThan(1902), [1901, 1902, 1903, 1904]],
            [isGreaterThan(1903), isLessThanOrEqualTo(1902), [1900, 1901, 1902, 1904]],
            [isEqualTo(1902), isEqualTo(1903), [1902, 1903]],
            [isEqualTo(1902), isNotEqualTo(1902), [1900, 1901, 1902, 1903, 1904]],
            [isEqualTo(1902), isNotEqualTo(1903), [1900, 1901, 1902, 1904]],
            [isGreaterThan(1903), isLessThanOrEqualTo(1902), [1900, 1901, 1902, 1904]],
        ];
    }

    #[DataProvider('dataProviderForTestCombinedOrNumericConditions')]
    public function testCombinedOrNumericConditions(NumericConditionInterface $condition1, NumericConditionInterface $condition2, $expected): void
    {
        $query = OrmManager::getInstance()
            ->getQueryFactory()
            ->getDummyEntityQuery();

        $entities = $query->where($query->orGroup()
            ->or(yearOfBirth: $condition1)
            ->or(yearOfBirth: $condition2)
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
