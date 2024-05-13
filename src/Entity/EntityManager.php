<?php

namespace Sarue\Orm\Entity;

use PgSql\Connection;
use Sarue\Orm\Entity\Type\EntityTypeInterface;
use Sarue\Orm\Exception\EntityNotFoundException;

class EntityManager
{
    public function __construct(
        protected Connection $connection,
    ) {}

    public function load(EntityTypeInterface $entityType, string $id): Entity
    {
        $result = pg_query($this->connection, 'SELECT * FROM ' . $entityType->getName() . " WHERE id = '" . $id . '"');
        if ($values = pg_fetch_array($result)) {
            return new Entity($entityType, pg_fetch_array($result));
        }

        throw new EntityNotFoundException('Entity not found.');
    }

    public function save(Entity $entity): void
    {
        $entityType = $entity->getEntityType();
        $columns = array_keys($entity->getValues());
        $values = array_map(
            fn($value) => "'" . $value . "'",
            $entity->getValues(),
        );

        if ($entity->getId()) {
            $updateFields = array_map(
                fn($column, $value) => $column . '=' . $value,
                $columns,
                $values,
            );
            pg_query($this->connection, 'UPDATE ' . $entityType->getName() . ' SET ' . implode(',' , $updateFields) . " WHERE id = '" . $entity->getId() . "'");
        } else {
            $result = pg_query($this->connection, 'INSERT INTO ' . $entityType->getName() . ' (' . implode(',', $columns) . ') VALUES(' . implode(',', $values) . ') RETURNING id');
            $id = pg_fetch_object($result)->id;
            $entity->setId($id);
        }

        $columns[] = 'id';
        $values[] = "'" . $entity->getId() . "'";
        pg_query($this->connection, 'INSERT INTO ' . $entityType->getName() . '__revision (' . implode(',', $columns) . ') VALUES(' . implode(',', $values) . ') RETURNING id');
    }
}
