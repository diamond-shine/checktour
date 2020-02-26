<?php

namespace Shelter\Kernel\Console\Commands\Spm;

use Illuminate\Console\Command;
use Throwable;

class MakeMigration extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'spm:make:migration {package_name} {name} {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make package migration';

    /**
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        $packageName = $this->argument('package_name');
        $name = $this->argument('name');

        $packagePath = \base_path("packages/{$packageName}");

        if (! \is_dir($packagePath)) {
            $this->error("Package with name [{$packageName}] nof exists");

            return;
        }

        $migrationsPath = "{$packagePath}/database/migrations";

        if (! is_dir($migrationsPath)
            && ! mkdir($migrationsPath, 0755, true)
            && ! is_dir($migrationsPath)
        ) {
            throw new \RuntimeException("Directory \"{$migrationsPath}\" was not created");
        }

        $this->call('make:migration', [
            'name' => $name,
            '--path' => $migrationsPath,
            '--realpath' => true,
            '--table' => $this->option('table'),
        ]);

        $this->info('Migration created');
    }
}
