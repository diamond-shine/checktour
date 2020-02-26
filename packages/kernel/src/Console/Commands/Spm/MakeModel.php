<?php

namespace Shelter\Kernel\Console\Commands\Spm;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Throwable;

class MakeModel extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'spm:make:model {package_name} {model_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make package model';

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
        $packageName = $this->argument('package_name');
        $modelName = $this->argument('model_name');

        $relativePath = "packages/{$packageName}";
        $path = \base_path($relativePath);

        if (! \is_dir($path)) {
            $this->files->makeDirectory($relativePath, 0755, true);
        }

        $this->makeModel($relativePath, $packageName, $modelName);

        $this->makeContextModel(
            $relativePath,
            $packageName,
            $modelName,
            'control'
        );

        $this->makeContextModel(
            $relativePath,
            $packageName,
            $modelName,
            'front'
        );

        $morphName = Str::singular($modelName) . '::morphMapType()';

        $this->info("Morph map (Add this to service provider):\n");

        $this->warn(
            sprintf(
                "use Shelter\\%s\\Models\\Control\\%s as Control%s;", Str::studly($packageName),
                $modelName,
                $modelName
            )
        );
        $this->warn(
            sprintf(
                "use Shelter\\%s\\Models\\Front\\%s as Front%s;", Str::studly($packageName),
                $modelName,
                $modelName
            )
        );

        $this->warn('...');

        $this->warn("{$morphName} => [");
        $this->warn("    'control' => Control{$modelName}::class,");
        $this->warn("    'front' => Front{$modelName}::class,");
        $this->warn('],');

        $this->info("\nModel created\n");
    }

    /**
     * @param string $relativePath
     * @param string $packageName
     * @param string $modelName
     * @throws Throwable
     */
    protected function makeModel(string $relativePath, string $packageName, string $modelName): void
    {
        $modelPath = "{$relativePath}/src/Models/{$modelName}.php";

        if ($this->files->exists($modelPath)
            && ! $this->confirm('Model already exists. Override?')
        ) {
            return;
        }

        if (! $this->files->exists("{$relativePath}/src/Models")) {
            $this->files->makeDirectory("{$relativePath}/src/Models", 0755, true);
        }

        $this->files->put(
            $modelPath,
            $this->renderStub('BaseModel', [
                'packageName' => $packageName,
                'modelName' => $modelName,
            ], true)
        );
    }

    /**
     * @param string $relativePath
     * @param string $packageName
     * @param string $modelName
     * @param string $type
     * @throws Throwable
     */
    protected function makeContextModel(
        string $relativePath,
        string $packageName,
        string $modelName,
        string $type
    ): void {
        $type = Str::studly($type);

        $modelPath = "{$relativePath}/src/Models/{$type}/{$modelName}.php";

        if ($this->files->exists($modelPath)
            && ! $this->confirm("{$type} model already exists. Override?")
        ) {
            return;
        }

        if (! $this->files->exists("{$relativePath}/src/Models/{$type}")) {
            $this->files->makeDirectory("{$relativePath}/src/Models/{$type}", 0755, true);
        }

        $this->files->put(
            $modelPath,
            $this->renderStub("{$type}Model", [
                'packageName' => $packageName,
                'modelName' => $modelName,
            ], true)
        );
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
