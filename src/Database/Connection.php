<?php

namespace Sarue\Orm\Database;

use PgSql\Connection as PgSqlConnection;
use PgSql\Result as PgSqlResult;

class Connection
{
    protected PgSqlConnection $internalConnection;

    public function __construct(
        ?string $dbname = null,
        ?string $user = null,
        ?string $password = null,
        ?string $host = 'localhost',
        ?int $port = 5432,
        ?int $connect_timeout = 0,
        ?string $sslmode = null,
        ?string $service = null,
        ?string $hostaddr = null,
        ?string $options = '--client_encoding=UTF8',
    ) {
        $parameters = [
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password,
            'host' => $host,
            'port' => $port,
            'connect_timeout' => $connect_timeout,
            'sslmode' => $sslmode,
            'service' => $service,
            'hostaddr' => $hostaddr,
            'options' => $options ? "'$options'" : null,
        ];

        $parameters = array_filter($parameters);
        $connectionString = implode(' ', array_map(
            fn($parameter, $parameterName) => "$parameterName=$parameter",
            $parameters,
            array_keys($parameters),
        ));

        $internalConnection = pg_connect($connectionString);
        if (false === $internalConnection) {
            throw new \Exception('It was not possible to connect to PostgreSQL.');
        }
        $this->internalConnection = $internalConnection;
    }

    public function runRaw(string $query, array $parameters = []): PgSqlResult|false
    {
        foreach (array_keys($parameters) as $i => $parameterName) {
            $query = str_replace($parameterName, '$' . ($i + 1), $query);
        }
        print "$query\n";

        return pg_query_params($this->internalConnection, $query, array_values($parameters));
    }

    /*
    public function createTable(string $tableName): CreateTable
    {
        return new CreateTable($this, $this->schema->getTable($tableName));
    }
    */

    public function select(string $tableName): Select
    {
        return new Select($this, $this->schema->getTable($tableName));
    }

    // public function insert()
    // public function update()
    // public function delete()
}
