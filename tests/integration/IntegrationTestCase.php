<?php

namespace Sarue\Orm\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\EntityManager\Generator\ClassGenerator;
use Sarue\Orm\OrmManager;

class IntegrationTestCase extends TestCase {
    protected Connection $connection;
    protected OrmManager $ormManager;

    public function setUp(): void
    {
        $databaseCreationconnection = pg_connect('host=localhost port=5432 user=postgres password=sarue');
        pg_exec($databaseCreationconnection, 'DROP DATABASE IF EXISTS sarue_integration_test_db');
        pg_exec($databaseCreationconnection, 'CREATE DATABASE sarue_integration_test_db');

        $classGenerator = new ClassGenerator(
            __DIR__ . '/Dummy/Entity',
            __DIR__ . '/var/sarue-generated',
            'Sarue\\Orm\\Tests\\Integration\\Dummy\\Entity\\',
            'Sarue\\Orm\\Tests\\Integration\\Dummy\\Generated\\',
        );
        $classGenerator->generateClasses();

        $this->connection = DriverManager::getConnection([
            'dbname' => 'sarue_integration_test_db ',
            'user' => 'postgres',
            'password' => 'sarue',
            'host' => 'localhost',
            'driver' => 'pdo_pgsql',
            'port' => 5432,
        ]);

        $this->ormManager = new OrmManager($this->connection);

        $this->ormManager->createTables();
    }
}
