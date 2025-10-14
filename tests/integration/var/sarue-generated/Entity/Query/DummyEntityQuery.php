<?php

namespace Sarue\Orm\Tests\Integration\Dummy\Generated\Entity\Query;

class DummyEntityQuery extends \Sarue\Orm\Query\QueryBase {
public function age(\Sarue\Orm\Query\Condition\Numeric\NumericConditionInterface $condition): static { return $this->addCondition($condition); }
public function name(\Sarue\Orm\Query\Condition\Text\TextConditionInterface $condition): static { return $this->addCondition($condition); }

public function getFieldList(): array {
return [
'age',
'name',
        ];
    }
}
