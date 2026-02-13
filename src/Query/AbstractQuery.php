<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Query\Condition\FieldConditionInterface;
use Sarue\Orm\Query\Condition\Group\AbstractConditionGroup;
use Sarue\Orm\Query\Sort\FieldSortExpressionInterface;
use Sarue\Orm\Query\Sort\SortExpressionInterface;

abstract class AbstractQuery extends AbstractConditionGroup implements QueryInterface
{
    protected const array QUERY_METHODS = ['where', 'and'];

    /**
     * @var SortExpressionInterface[]
     */
    protected array $sortExpressions = [];

    public function __call(string $name, array $arguments): static
    {
        if ('orderBy' === $name) {
            $this->addSortExpressions($arguments);

            return $this;
        }

        return parent::__call($name, $arguments);
    }

    public function hasWhere(): bool
    {
        return !empty($this->conditions);
    }

    public function getSortExpressions(): array
    {
        return $this->sortExpressions;
    }

    protected function doLoadById(string $id)
    {
        return $this->ormManager->loadById($this, $id);
    }

    protected function doLoadAll()
    {
        return $this->ormManager->loadAll($this);
    }

    protected function addSortExpressions(array $sortExpressions): void
    {
        $fields = $this->ormManager->getFieldDefinitions(static::ENTITY_CLASS);

        foreach (array_filter($sortExpressions) as $fieldName => $sortExpression) {
            if ('_sort' !== $fieldName) {
                if (empty($fields[$fieldName])) {
                    throw new \BadMethodCallException(sprintf('"%s" is not a valid field name for entity "%s".', $fieldName, static::ENTITY_CLASS));
                } elseif (!($sortExpression instanceof FieldSortExpressionInterface)) {
                    throw new \BadMethodCallException(sprintf('Condition for field "%s" in entity "%s" must implement interface %s.', $fieldName, FieldConditionInterface::class));
                }

                $sortExpression->setFieldName($fieldName);
            }
            $this->sortExpressions[] = $sortExpression;
        }
    }
}
