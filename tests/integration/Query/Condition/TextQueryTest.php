<?php

namespace Sarue\Orm\Tests\Integration\Query\Condition;

use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\Text\UnescapedText;
use Sarue\Orm\Tests\Integration\Dummy\Entity\DummyLogEntity;
use Sarue\Orm\Tests\Integration\IntegrationTestCase;

use function Sarue\Orm\Query\Condition\contains;
use function Sarue\Orm\Query\Condition\doesNotContain;
use function Sarue\Orm\Query\Condition\doesNotEndWith;
use function Sarue\Orm\Query\Condition\doesNotStartWith;
use function Sarue\Orm\Query\Condition\endsWith;
use function Sarue\Orm\Query\Condition\like;
use function Sarue\Orm\Query\Condition\notLike;
use function Sarue\Orm\Query\Condition\startsWith;
use function Sarue\Orm\Query\Sort\asc;

class TextQueryTest extends IntegrationTestCase
{
    public function testAllTextConditions()
    {
        $messages = [
            'dolor sit amet',
            'DOLOR SIT AMET',
            'Lorem ipsum',
            'LOREM IPSUM',
            'LOREM%IP_SUM',
            'LOREM_IP%SUM',
            'Lorem ipsum dolor sit amet',
            'Lorentz Ipsums',
        ];

        foreach ($messages as $message) {
            $entity = new DummyLogEntity();
            $entity->message = $message;
            $entity->save();
        }

        // Not using DataProvider so that the test runs more quickly with just
        // one batch of insertions.
        $testCases = [
            [
                startsWith('lorem ipsum'),
                [],
            ],
            [
                startsWith('Lorem ipsum'),
                [
                    'Lorem ipsum',
                    'Lorem ipsum dolor sit amet',
                ],
            ],
            [
                startsWith('lorem ipsum', true),
                [
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'Lorem ipsum dolor sit amet',
                ],
            ],
            [
                doesNotStartWith('Lorem ipsum'),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorentz Ipsums',
                ],
            ],
            [
                doesNotStartWith('Lorem ipsum', true),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorentz Ipsums',
                ],
            ],
            [
                endsWith('dolor sit amet'),
                [
                    'dolor sit amet',
                    'Lorem ipsum dolor sit amet',
                ],
            ],
            [
                endsWith('dolor sit amet', true),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'Lorem ipsum dolor sit amet',
                ],
            ],
            [
                doesNotEndWith('sit amet'),
                [
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorentz Ipsums',
                ],
            ],
            [
                doesNotEndWith('sit amet', true),
                [
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorentz Ipsums',
                ],
            ],
            [
                contains('ipsum'),
                [
                    'Lorem ipsum',
                    'Lorem ipsum dolor sit amet',
                ],
            ],
            [
                contains('ipsum', true),
                [
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                doesNotContain('sit'),
                [
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorentz Ipsums',
                ],
            ],
            [
                doesNotContain('sit', true),
                [
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorentz Ipsums',
                ],
            ],
            [
                like('LOREM_IP%SUM'),
                [
                    'LOREM_IP%SUM',
                ],
            ],
            [
                like(new UnescapedText('LOREM_IP%SUM')),
                [
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                ],
            ],
            [
                like(new UnescapedText('LOREM_IP%SUM'), true),
                [
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                ],
            ],
            [
                notLike('Lorem ipsum'),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                notLike('Lorem ipsum', true),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                notLike(new UnescapedText('Lorem ipsum'), true),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'LOREM%IP_SUM',
                    'LOREM_IP%SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            // Tests that the text is escaped.
            [
                startsWith('LOREM_IP%SUM'),
                [
                    'LOREM_IP%SUM',
                ],
            ],
            [
                startsWith('LOREM_IP%SUM', true),
                [
                    'LOREM_IP%SUM',
                ],
            ],
            [
                doesNotStartWith('LOREM_IP%SUM'),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                doesNotStartWith('LOREM_IP%SUM', true),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                endsWith('LOREM_IP%SUM'),
                [
                    'LOREM_IP%SUM',
                ],
            ],
            [
                endsWith('LOREM_IP%SUM', true),
                [
                    'LOREM_IP%SUM',
                ],
            ],
            [
                doesNotEndWith('LOREM_IP%SUM'),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                doesNotEndWith('LOREM_IP%SUM', true),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                contains('LOREM_IP%SUM'),
                [
                    'LOREM_IP%SUM',
                ],
            ],
            [
                contains('LOREM_IP%SUM', true),
                [
                    'LOREM_IP%SUM',
                ],
            ],
            [
                doesNotContain('LOREM_IP%SUM'),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
            [
                doesNotContain('LOREM_IP%SUM', true),
                [
                    'dolor sit amet',
                    'DOLOR SIT AMET',
                    'Lorem ipsum',
                    'LOREM IPSUM',
                    'LOREM%IP_SUM',
                    'Lorem ipsum dolor sit amet',
                    'Lorentz Ipsums',
                ],
            ],
        ];

        foreach ($testCases as $testCase) {
            [$condition, $expected] = $testCase;

            $entities = OrmManager::getInstance()
                ->getQueryFactory()
                ->getDummyLogEntityQuery()
                ->where(
                    message: $condition,
                )
                ->orderBy(message: asc())
                ->loadAll()
            ;
            $this->assertEquals(
                $expected,
                array_map(
                    fn (DummyLogEntity $entity) => $entity->message,
                    $entities,
                ),
            );
        }
    }
}
