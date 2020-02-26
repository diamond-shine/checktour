<?php

namespace Shelter\Kernel\Console\Commands\Spm;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class MakeHotRelation extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'spm:make:hot-relation {package_name} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make hot relation';

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
        $name = $this->argument('name');

        $hotRelationName = $this->generate(
            $packageName,
            $this->resolvePackagePath($packageName),
            $name
        );

        if ($hotRelationName) {
            $this->info("Hot relation created created\n");

            $this->warn("Don't forget modify manifest file\n");
        }
    }

    /**
     * @param string $packageName
     * @param string $packagePath
     * @param string $name
     * @return string|null
     * @throws Throwable
     */
    protected function generate(string $packageName, string $packagePath, string $name): ?string
    {
        $baseName = Str::studly($name) . 'HotRelations';

        $path = "{$packagePath}/src/HotRelations/{$baseName}.php";

        if ($this->files->exists($path)
            && ! $this->confirm('Hot relation already exists. Override?')
        ) {
            return null;
        }

        if (! $this->files->isDirectory("{$packagePath}/src/HotRelations")) {
            $this->files->makeDirectory("{$packagePath}/src/HotRelations", 0755, true);
        }

        $this->files->put(
            $path,
            $this->renderStub('HotRelations__php', [
                'packageName' => $packageName,
                'baseName' => $baseName,
            ], true)
        );

        return $baseName;
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

    /**
     * @param string $name
     * @return string
     */
    protected function resolvePackagePath(string $name): string
    {
        $path = \base_path("packages/{$name}");

        if (! \is_dir($path)) {
            throw new RuntimeException("Package [{$name}] does not exists");
        }

        return $path;
    }
}
