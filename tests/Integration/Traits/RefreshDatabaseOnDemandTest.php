<?php

declare(strict_types=1);

namespace Tests\Integration\Traits;

use EndeavourAgency\RefreshDatabaseOnDemand\Traits\RefreshDatabaseOnDemand;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Mockery;
use Mockery\MockInterface;
use Orchestra\Testbench\Concerns\ApplicationTestingHooks;
use Orchestra\Testbench\Foundation\Application as Testbench;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function Orchestra\Testbench\package_path;

class RefreshDatabaseOnDemandTest extends TestCase
{
    use ApplicationTestingHooks;
    use InteractsWithConsole;
    use RefreshDatabaseOnDemand;
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected Migrator&MockInterface $migrator;

    protected function setUp(): void
    {
        $this->setUpTheApplicationTestingHooks();
        $this->withoutMockingConsoleOutput();

        RefreshDatabaseState::$migrated = false;

        $this->migrator = Mockery::mock(Migrator::class);

        $this->app->instance('migrator', $this->migrator);
    }

    protected function tearDown(): void
    {
        $this->tearDownTheApplicationTestingHooks();

        RefreshDatabaseState::$migrated = false;
    }

    protected function refreshApplication()
    {
        $this->app = Testbench::create(
            basePath: package_path('vendor/orchestra/testbench-core/laravel'),
        );
    }

    #[Test]
    public function it_runs_migrate_fresh_if_no_migrations_have_run_yet(): void
    {
        $this->app->instance(ConsoleKernelContract::class, $kernel = Mockery::spy(ConsoleKernel::class));

        $this->migrator
            ->shouldReceive('repositoryExists')
            ->once()
            ->andReturnFalse();

        $kernel->shouldReceive('call')
            ->once()
            ->with(
                'migrate:fresh',
                [
                    '--drop-views' => false,
                    '--drop-types' => false,
                    '--seed'       => false,
                ],
            );

        $this->refreshTestDatabase();
    }

    #[Test]
    public function it_runs_migrate_fresh_if_a_new_migration_has_been_added(): void
    {
        $this->app->instance(ConsoleKernelContract::class, $kernel = Mockery::spy(ConsoleKernel::class));

        $this->migrator
            ->shouldReceive('repositoryExists')
            ->once()
            ->andReturnTrue();

        $this
            ->migrator
            ->shouldReceive('paths')
            ->once()
            ->andReturn([]);

        $this
            ->migrator
            ->shouldReceive('getMigrationFiles')
            ->once()
            ->andReturn([
                '0001_01_01_000000_testbench_create_users_table' => '0001_01_01_000000_testbench_create_users_table.php',
                '0001_01_01_000001_testbench_create_cache_table' => '0001_01_01_000001_testbench_create_cache_table.php',
                '0001_01_01_000002_testbench_create_jobs_table'  => '0001_01_01_000002_testbench_create_jobs_table.php',
            ]);

        $migrationRepository = Mockery::mock(MigrationRepositoryInterface::class);
        $migrationRepository
            ->shouldReceive('getRan')
            ->once()
            ->andReturn([
                '0001_01_01_000000_testbench_create_users_table',
                '0001_01_01_000001_testbench_create_cache_table',
            ]);

        $this->migrator
            ->shouldReceive('getRepository')
            ->once()
            ->andReturn($migrationRepository);

        $kernel->shouldReceive('call')
            ->once()
            ->with(
                'migrate:fresh',
                [
                    '--drop-views' => false,
                    '--drop-types' => false,
                    '--seed'       => false,
                ],
            );

        $this->refreshTestDatabase();
    }

    #[Test]
    public function it_runs_migrate_fresh_if_a_migration_has_been_deleted(): void
    {
        $this->app->instance(ConsoleKernelContract::class, $kernel = Mockery::spy(ConsoleKernel::class));

        $this->migrator
            ->shouldReceive('repositoryExists')
            ->once()
            ->andReturnTrue();

        $this
            ->migrator
            ->shouldReceive('paths')
            ->once()
            ->andReturn([]);

        $this
            ->migrator
            ->shouldReceive('getMigrationFiles')
            ->once()
            ->andReturn([
                '0001_01_01_000000_testbench_create_users_table' => '0001_01_01_000000_testbench_create_users_table.php',
                '0001_01_01_000001_testbench_create_cache_table' => '0001_01_01_000001_testbench_create_cache_table.php',
            ]);

        $migrationRepository = Mockery::mock(MigrationRepositoryInterface::class);
        $migrationRepository
            ->shouldReceive('getRan')
            ->once()
            ->andReturn([
                '0001_01_01_000000_testbench_create_users_table',
                '0001_01_01_000001_testbench_create_cache_table',
                '0001_01_01_000002_testbench_create_jobs_table',
            ]);

        $this->migrator
            ->shouldReceive('getRepository')
            ->once()
            ->andReturn($migrationRepository);

        $kernel->shouldReceive('call')
            ->once()
            ->with(
                'migrate:fresh',
                [
                    '--drop-views' => false,
                    '--drop-types' => false,
                    '--seed'       => false,
                ],
            );

        $this->refreshTestDatabase();
    }

    #[Test]
    public function it_does_not_run_migrate_fresh_if_migration_files_are_same_as_ran_migrations(): void
    {
        $this->app->instance(ConsoleKernelContract::class, $kernel = Mockery::spy(ConsoleKernel::class));

        $this->migrator
            ->shouldReceive('repositoryExists')
            ->once()
            ->andReturnTrue();

        $this
            ->migrator
            ->shouldReceive('paths')
            ->once()
            ->andReturn([]);

        $this
            ->migrator
            ->shouldReceive('getMigrationFiles')
            ->once()
            ->andReturn([
                '0001_01_01_000000_testbench_create_users_table' => '0001_01_01_000000_testbench_create_users_table.php',
                '0001_01_01_000001_testbench_create_cache_table' => '0001_01_01_000001_testbench_create_cache_table.php',
            ]);

        $migrationRepository = Mockery::mock(MigrationRepositoryInterface::class);
        $migrationRepository
            ->shouldReceive('getRan')
            ->once()
            ->andReturn([
                '0001_01_01_000000_testbench_create_users_table',
                '0001_01_01_000001_testbench_create_cache_table',
            ]);

        $this->migrator
            ->shouldReceive('getRepository')
            ->once()
            ->andReturn($migrationRepository);

        $kernel->shouldNotReceive('call')
            ->with(
                'migrate:fresh',
                [
                    '--drop-views' => false,
                    '--drop-types' => false,
                    '--seed'       => false,
                ],
            );

        $this->refreshTestDatabase();
    }
}
