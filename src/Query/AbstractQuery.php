<?php

namespace Sarue\Orm\Query;

use Sarue\Orm\Entity\EntityInterface;
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

    protected function doLoadById(string $id): EntityInterface
    {
        return $this->ormManager->loadById($this, $id);
    }

    /**
     * @return EntityInterface[]
     */
    protected function doLoadAll(): array
    {
        return $this->ormManager->loadAll($this);
    }

    /**
     * @param mixed[] $sortExpressions
     */
    protected function addSortExpressions(array $sortExpressions): void
    {
        $fields = $this->ormManager->getFieldDefinitions($this->getEntityClass());

        foreach (array_filter($sortExpressions) as $fieldName => $sortExpression) {
            if (!($sortExpression instanceof SortExpressionInterface)) {
                throw new \BadMethodCallException(sprintf('Sort expression for field "%s" in entity "%s" must implement interface %s.', $fieldName, $this->getEntityClass(), SortExpressionInterface::class));
            }

            if ('_sort' !== $fieldName) {
                if (empty($fields[$fieldName])) {
                    throw new \BadMethodCallException(sprintf('"%s" is not a valid field name for entity "%s".', $fieldName, $this->getEntityClass()));
                } elseif (!($sortExpression instanceof FieldSortExpressionInterface)) {
                    throw new \BadMethodCallException(sprintf('Sort expression for field "%s" in entity "%s" must implement interface %s.', $fieldName, $this->getEntityClass(), FieldSortExpressionInterface::class));
                }

                $sortExpression->setFieldName($fieldName);
            }
            $this->sortExpressions[] = $sortExpression;
        }
    }
}
