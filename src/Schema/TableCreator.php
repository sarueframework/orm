<?php

namespace Sarue\Orm\Schema;

use PgSql\Connection;
use Sarue\Orm\Entity\Type\EntityTypeInterface;
use Sarue\Orm\Field\FieldInterface;

class TableCreator
{
    public function __construct(
        protected Connection $connection
    ) {
    }

    public function createTable(EntityTypeInterface $entityType): void
    {
        $fieldsSql = implode(',', array_map(
            fn(FieldInterface $field): string => $field->sqlColumnCode(),
            $entityType->getFields(),
        ));

        $mainTableSql = 'CREATE TABLE ' . $entityType->getTableName() . '( id uuid PRIMARY KEY DEFAULT uuid_generate_v4(), ' . $fieldsSql . ')';
        $revisionTableSql = 'CREATE TABLE ' . $entityType->getRevisionTableName() . '(id uuid references ' . $entityType->getName() . '(id), revision_id uuid PRIMARY KEY DEFAULT uuid_generate_v4(),' . $fieldsSql . ')';

        pg_query($this->connection, 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        pg_query($this->connection, $mainTableSql);
        pg_query($this->connection, $revisionTableSql);
    }
}
