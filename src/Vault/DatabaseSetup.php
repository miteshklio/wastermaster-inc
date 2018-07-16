<?php namespace Vault;

use Illuminate\Contracts\Console\Kernel;

/**
 * DatabaseSetup Trait
 *
 * A replacement for Laravel's built-in Database tools for
 * testing that will run migrations only once, and then wrap
 * all other tests within a transaction for better performance.
 */
trait DatabaseSetup
{
    protected static $migrated = false;

    /**
     * The main entry point that will setup
     * and migrate our database, if they haven't already been.
     */
    public function setupDatabase()
    {
        if ($this->isInMemory()) {
            $this->setupInMemoryDatabase();
        } else {
            $this->setupTestDatabase();
        }
    }

    /**
     * Checks to see if the database is an in-memory database,
     * typically from an SQLite database.
     *
     * @return bool
     */
    protected function isInMemory()
    {
        return config('database.connections')[config('database.default')]['database'] == ':memory:';
    }

    /**
     * Runs migrations on in-memory databases,
     * which have to be re-ran every time since the
     * database itself is ephemeral.
     */
    protected function setupInMemoryDatabase()
    {
        $this->artisan('migrate');
//        $this->app[Kernel::class]->setArtisan(null);
    }

    /**
     * Runs migrations on a non-in-memory databases.
     */
    protected function setupTestDatabase()
    {
        if (!static::$migrated) {
            $this->artisan('migrate:refresh');
//            $this->app[Kernel::class]->setArtisan(null);
            static::$migrated = true;
        }
        $this->beginDatabaseTransaction();
    }


    public function beginDatabaseTransaction()
    {
        $database = $this->app->make('db');

        foreach ($this->connectionsToTransact() as $name) {
            $database->connection($name)->beginTransaction();
        }

        $this->beforeApplicationDestroyed(function () use ($database) {
            foreach ($this->connectionsToTransact() as $name) {
                $database->connection($name)->rollBack();
            }
        });
    }

    protected function connectionsToTransact()
    {
        return property_exists($this, 'connectionsToTransact')
            ? $this->connectionsToTransact : [null];
    }
}
