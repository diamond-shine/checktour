<?php

namespace Shelter\Kernel\Console\Commands\Spm;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Remove extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'spm:remove {package? : The name of the package}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove package';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Discover constructor.
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;

        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        if (! $this->checkComposer()) {
            $this->error('Composer not found');

            return;
        }

        $this->info('Start removing package');

        $packageName = $this->argument('package');

        $installedPackages = \array_map(
            function (string $package) {
                return \str_replace('shelter/', '', $package);
            },
            \array_keys(
                app('shelter.packages')->repository()->all()
            )
        );

        if (empty($installedPackages)) {
            $this->warn('Nothing to install...');

            return;
        }

        if (! $packageName) {
            $packageName = $this->choice('Select package', $installedPackages);
        }

        if (! Str::contains($packageName, '/')) {
            $packageName = "shelter/{$packageName}";
        }

        if (! app('shelter.packages')->repository()->has($packageName)) {
            $this->error("Package [{$packageName}] not installed");
        }

        $this->removePackage($packageName);
    }

    /**
     * @param string $packageName
     */
    protected function removePackage(string $packageName): void
    {
        $this->warn('  - Preparing');

        $process = new Process(['composer', 'remove', $packageName]);

        $process->setTimeout(0);

        $process->run(function (string $type, string $message) use ($packageName) {
            $statuses = [
                'Loading composer repositories with package information' => 'Loading packages information',
                "- Removing {$packageName}" => 'Removing',
            ];

            foreach ($statuses as $composerMessage => $status) {
                if (Str::contains($message, $composerMessage)) {
                    $this->warn("  - {$status}");
                }
            }
        });

        if ($this->files->exists('var/app/shelter/package-manifest.php')) {
            $this->files->delete('var/app/shelter/package-manifest.php');
        }

        $this->call('spm:discover');

        if ($this->confirm('Remove package folder?')) {
            $shortName = \explode('/', $packageName)[1];

            $this->files->deleteDirectory("packages/{$shortName}");
        }

        $this->info("\nPackage [$packageName] successfully removed");
    }

    /**
     * @return bool
     */
    protected function checkComposer(): bool
    {
        $process = new Process(['composer', '-v']);

        $process->setTimeout(0);

        $process->run();

        return $process->isSuccessful();
    }
}
