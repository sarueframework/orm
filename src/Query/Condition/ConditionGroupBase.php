<?php

namespace Sarue\Orm\Query\Condition;

class ConditionGroupBase implements ConditionInterface {
    /**
     * @var \Sarue\Orm\Query\Condition\ConditionInterface[]
     */
    protected array $conditions = [];

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
