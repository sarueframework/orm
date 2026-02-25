<?php

namespace Sarue\Orm\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;
use Sarue\Orm\Generator\ClassGenerator;
use Sarue\Orm\OrmManager;

class IntegrationTestCase extends TestCase
{
    protected const bool HAS_DATABASE = true;

    protected const bool CREATE_TABLES = true;

    final protected const string ENTITY_SET = 'Entity';

    final protected const GENERATED_FOLDER = __DIR__.'/var/sarue-generated';

    final protected const BASE_ENTITY_NAMESPACE = 'Sarue\\Orm\\Tests\\Integration\\Dummy\\';

    protected static ?Connection $connection;

    /**
     * {@inheritdoc}
     *
     * Using setUpBeforeClass() is not recommended by PHPUnit documentation.
     * However, we are using PHPUnit to run *integration* tests in this suite,
     * instead of proper unitary tests. Therefore, it makes sense to use a more
     * global set up so that we save some time on database creation/deletion.
     */
    public static function setUpBeforeClass(): void
    {
        // Tests if the Orm Manager is initialized, i.e., if this is the first
        // test run. If not, create the definitions and initialize ORM.
        try {
            OrmManager::getInstance();
        }
        catch (\Exception $exception) {
            static::createDefinitions(static::ENTITY_SET);
        }

        if (isset(static::$connection)) {
            static::$connection->close();
        }

        if (static::HAS_DATABASE) {
            static::createDatabase();
        }

        if (static::HAS_DATABASE && static::CREATE_TABLES) {
            static::createTables();
        }
    }

    protected static function createDatabase(): void
    {
        $databaseCreationconnection = pg_connect('host=localhost port=5432 user=postgres password=sarue');
        pg_exec($databaseCreationconnection, 'DROP DATABASE IF EXISTS sarue_integration_test_db');
        pg_exec($databaseCreationconnection, 'CREATE DATABASE sarue_integration_test_db');

        static::$connection = DriverManager::getConnection([
            'dbname' => 'sarue_integration_test_db',
            'user' => 'postgres',
            'password' => 'sarue',
            'host' => 'localhost',
            'driver' => 'pdo_pgsql',
            'port' => 5432,
        ]);

        new OrmManager(static::$connection);
    }

    protected static function createDefinitions(string $entitySet): void
    {
        $classGenerator = new ClassGenerator(
            __DIR__.'/Dummy/'.$entitySet,
            static::GENERATED_FOLDER,
            static::BASE_ENTITY_NAMESPACE.$entitySet.'\\',
        );
        $classGenerator->generateClasses();
    }

    protected static function createTables(): void
    {
        OrmManager::getInstance()->createTables();
    }
}
