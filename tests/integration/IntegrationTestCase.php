<?php

namespace Sarue\Orm\Tests\Integration;

use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase {
    protected $connection;

    public function setUp(): void
    {
        $databaseCreationconnection = pg_connect('host=localhost port=5432 user=postgres password=sarue');
        pg_exec($databaseCreationconnection, 'DROP DATABASE IF EXISTS sarue_integration_test_db');
        pg_exec($databaseCreationconnection, 'CREATE DATABASE sarue_integration_test_db');
        $this->connection = pg_connect('host=localhost port=5432 dbname=sarue_integration_test_db user=postgres password=sarue');
    }
}
