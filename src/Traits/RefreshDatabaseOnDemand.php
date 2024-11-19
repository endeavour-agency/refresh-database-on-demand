<?php

declare(strict_types=1);

namespace EndeavourAgency\RefreshDatabaseOnDemand\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait RefreshDatabaseOnDemand
{
    use RefreshDatabase {
        refreshTestDatabase as parentRefreshTestDatabase;
    }

    protected function refreshTestDatabase(): void
    {
        $shouldMigrate = $this->shouldMigrate();

        if (! $shouldMigrate) {
            RefreshDatabaseState::$migrated = true;
        }

        $this->parentRefreshTestDatabase();
    }

    /**
     * Checks whether there are any migration files that have not yet run.
     *
     * @return bool
     */
    protected function shouldMigrate(): bool
    {
        $migrator = app('migrator');

        if (! $migrator->repositoryExists()) {
            return true;
        }

        $migrationDirectories = array_merge($migrator->paths(), [database_path('migrations')]);
        $migrationFiles       = array_keys($migrator->getMigrationFiles($migrationDirectories));
        $ran                  = $migrator->getRepository()->getRan();

        return $ran !== $migrationFiles;
    }
}
