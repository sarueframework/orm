<?php

namespace Sarue\Orm\Query\Condition\Group;

use Sarue\Orm\OrmManager;
use Sarue\Orm\Query\Condition\ConditionInterface;
use Sarue\Orm\Query\Condition\FieldConditionInterface;

abstract class AbstractConditionGroup implements ConditionInterface
{
    public const string ENTITY_CLASS = '';

    protected const string CONJUNCTION = ' AND ';

    protected const array QUERY_METHODS = [];

    /**
     * @var ConditionInterface[]
     */
    protected array $conditions = [];

    final public function __construct(
        protected OrmManager $ormManager,
    ) {
    }

    public function __call(string $name, array $arguments): static
    {
        if (in_array($name, static::QUERY_METHODS)) {
            $this->addConditions($arguments);

            return $this;
        }
        throw new \BadMethodCallException('Call to undefined method '.static::class.'::'.$name.'()');
    }

    public function buildSql(): array
    {
        $sql = ['('];
        foreach ($this->conditions as $delta => $condition) {
            if ($delta) {
                $sql[] = static::CONJUNCTION;
            }
            $sql = array_merge($sql, $condition->buildSql());
        }
        $sql[] = ')';

        return $sql;
    }

    protected function addConditions(array $conditions): void
    {
        $fields = $this->ormManager->getFieldDefinitions(static::ENTITY_CLASS);

        foreach (array_filter($conditions) as $fieldName => $condition) {
            if ('_condition' !== $fieldName) {
                if (empty($fields[$fieldName])) {
                    throw new \BadMethodCallException(sprintf('"%s" is not a valid field name for entity "%s".', $fieldName, static::ENTITY_CLASS));
                } elseif (!is_subclass_of($condition, $fields[$fieldName]->getConditionType())) {
                    throw new \BadMethodCallException(sprintf('Condition for field "%s" in entity "%s" must implement interface %s.', $fieldName, static::ENTITY_CLASS, $fields[$fieldName]->getConditionType()));
                } elseif (!($condition instanceof FieldConditionInterface)) {
                    throw new \BadMethodCallException(sprintf('Condition for field "%s" in entity "%s" must implement interface %s.', $fieldName, static::ENTITY_CLASS, FieldConditionInterface::class));
                }

                $condition->setFieldName($fieldName);
            }
            $this->conditions[] = $condition;
        }
    }
}
