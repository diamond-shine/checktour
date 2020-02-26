<?php

namespace Shelter\Kernel\Console\Commands\Spm;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class Install extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'spm:install {package? : The name of the package}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install package';

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
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        if (! $this->checkComposer()) {
            $this->error('Composer not found');

            return;
        }

        $this->info('Start installing package');

        $packageName = $this->argument('package');

        $notInstalledPackages = $this->notInstalledPackages();

        if (empty($notInstalledPackages)) {
            $this->warn('Nothing to install...');

            return;
        }

        if (! $packageName) {
            $packageName = $this->choice('Select package', $notInstalledPackages);
        }

        if (! Str::contains($packageName, '/')) {
            $packageName = "shelter/{$packageName}";
        }

        if (app('shelter.packages')->repository()->has($packageName)) {
            $this->error("Package [{$packageName}] already installed");

            return;
        }

        if (! \in_array($packageName, $notInstalledPackages, true)) {
            $this->error("Package [{$packageName}] not found");

            return;
        }

        $this->installPackage($packageName);
    }

    /**
     * @param string $packageName
     */
    protected function installPackage(string $packageName): void
    {
        $this->warn('  - Preparing');

        $process = new Process(['composer', 'require', "{$packageName}:*"]);

        $process->setTimeout(0);

        $process->run(function (string $type, string $message) use ($packageName) {
            $statuses = [
                'Loading composer repositories with package information' => 'Loading packages information',
                "- Installing {$packageName}" => 'Installing',
            ];

            foreach ($statuses as $composerMessage => $status) {
                if (Str::contains($message, $composerMessage)) {
                    $this->warn("  - {$status}");
                }
            }
        });

        $this->call('spm:discover');

        $this->info("\nPackage [$packageName] successfully installed");
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

    /**
     * @return array
     * @throws FileNotFoundException
     */
    protected function notInstalledPackages(): array
    {
        $installedPackages = app('shelter.packages')->repository()->all();

        $allPackages = [];

        foreach ($this->files->directories('packages') as $directory) {
            if (! $this->files->exists("{$directory}/manifest.php")
                || ! $this->files->exists("{$directory}/composer.json")
            ) {
                continue;
            }

            $data = \json_decode(
                $this->files->get("{$directory}/composer.json"),
                true
            );

            $allPackages[] = $data['name'];
        }

        return \array_values(
            \array_diff(
                $allPackages,
                \array_keys($installedPackages)
            )
        );
    }
}
