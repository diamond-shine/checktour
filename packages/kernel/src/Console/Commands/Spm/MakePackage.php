<?php

namespace Shelter\Kernel\Console\Commands\Spm;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Throwable;

class MakePackage extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'spm:make:package {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make package';

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
     * @throws Throwable
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        try {
            $this->make($name);
        } catch (Throwable $e) {
            $this->files->deleteDirectory("packages/{$name}");

            throw $e;
        }

        $this->info('Package created');
    }

    /**
     * @param string $packageName
     * @throws Throwable
     */
    protected function make(string $packageName): void
    {
        $relativePath = "packages/{$packageName}";
        $path = \base_path($relativePath);

        $hasConfig = $this->confirm('Config file?', false);

        if (\is_dir($path)) {
            if (! $this->confirm('Package already exists. Override?')) {
                return;
            } else {
                $this->files->deleteDirectory($relativePath);
            }
        }

        if (! \is_dir($path)) {
            $this->files->makeDirectory($relativePath, 0755, true);
        }

        $this->files->put(
            "{$relativePath}/composer.json",
            $this->renderStub('composer__json', [
                'name' => $packageName,
            ])
        );

        $this->files->put(
            "{$relativePath}/.gitignore",
            $this->renderStub('__gitignore')
        );

        $this->files->put(
            "{$relativePath}/manifest.php",
            $this->renderStub('manifest__php', [
                'name' => $packageName,
                'hasConfig' => $hasConfig,
            ], true)
        );

        // Service provider
        $this->files->makeDirectory("{$relativePath}/src/Providers", 0755, true);

        $this->files->put(
            "{$relativePath}/src/Providers/" . Str::studly($packageName) . 'ServiceProvider.php',
            $this->renderStub('ServiceProvider__php', [
                'name' => $packageName,
            ], true)
        );

        // Config file
        if ($hasConfig) {
            $this->files->makeDirectory("{$relativePath}/resources/configs", 0755, true);

            $this->files->put(
                "{$relativePath}/resources/configs/" . \real_snake_case($packageName) . '.php',
                $this->renderStub('config', [], true)
            );
        }

        // Migration
        $this->callSilent('spm:make:migration', [
            'package_name' => $packageName,
            'name' => \sprintf(
                'create_%s_table',
                \real_snake_case($packageName)
            )
        ]);

        // Models
        $this->callSilent('spm:make:model', [
            'package_name' => $packageName,
            'model_name' => Str::studly(
                Str::singular($packageName)
            ),
        ]);
    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $isPhpFile
     * @return string
     * @throws Throwable
     */
    protected function renderStub(string $path, array $data = [], bool $isPhpFile = false): string
    {
        $content = \view("spm/stub::package.{$path}", $data)->render();

        if ($isPhpFile) {
            $content = "<?php\n\n{$content}";
        }

        return $content;
    }
}
