<?php

namespace Shelter\Kernel\Console\Commands\Spm;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class Discover extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'spm:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover installed shelter packages';

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
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->info('Start discover packages');

        $path = base_path('vendor/composer/installed.json');

        $installed = \json_decode(
            $this->files->get($path, true),
            true
        );

        $packages = [];

        foreach ($installed as $package) {
            if (! isset($package['extra']['shelter'])) {
                continue;
            }

            $packages[] = [
                'name' => $package['name'],
                'providers' => $package['extra']['shelter']['providers'] ?? null,
                'aliases' => $package['extra']['shelter']['aliases'] ?? null,
                'index' => $package['extra']['shelter']['index'] ?? 9999,
            ];

            $this->getOutput()->write("Discovered package: <fg=green>{$package['name']}</>", true);

            if ($package['extra']['shelter']['providers'] ?? null) {
                $this->getOutput()->write('  <fg=yellow>Providers:</>', true);

                foreach ($package['extra']['shelter']['providers'] as $provider) {
                    $this->getOutput()->write("    - <fg=green>{$provider}</>", true);
                }
            }

            if ($package['extra']['shelter']['aliases'] ?? null) {
                $this->getOutput()->write('  <fg=yellow>Aliases:</>', true);

                foreach ($package['extra']['shelter']['aliases'] as $alias => $facade) {
                    $this->getOutput()->write("    - <fg=green>{$alias}</> <fg=blue>-></> <fg=green>{$facade}</>", true);
                }
            }

            $this->info('');
        }

        $packages = \array_values(
            Arr::sort($packages, 'index')
        );

        $manifestDirPath = storage_path('app/shelter');

        if (! is_dir($manifestDirPath)) {
            $this->files->makeDirectory($manifestDirPath, 0755, true);
        }

        $this->files->put(
            storage_path('app/shelter/package-manifest.php'),
            '<?php return ' . var_export($packages, true) . ';',
            true
        );

        $this->getOutput()->write('<fg=green>Shelter package manifest generated successfully.</>', true);
    }
}
