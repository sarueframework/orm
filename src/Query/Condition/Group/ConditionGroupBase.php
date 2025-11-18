<?php

namespace Sarue\Orm\Query\Condition\Group;

use Sarue\Orm\Query\Condition\ConditionInterface;

abstract class ConditionGroupBase implements ConditionInterface
{
    public const string CONJUNCTION = ' AND ';

    /**
     * @var ConditionInterface[]
     */
    protected array $conditions = [];

    public function buildSql(): array
    {
        $sql = [];
        foreach ($this->conditions as $delta => $condition) {
            if ($delta) {
                $sql[] = static::CONJUNCTION;
            }
            $sql = array_merge($sql, $condition->buildSql());
        }

        return $sql;
    }

    protected function addConditions(?ConditionInterface $genericCondition, array $fieldConditions): static
    {
        if ($genericCondition) {
            $this->conditions[] = $genericCondition;
        }

        foreach (array_filter($fieldConditions) as $fieldName => $condition) {
            $condition->fieldName = $fieldName;
            $this->conditions[] = $condition;
        }

        return $this;
    }
}
