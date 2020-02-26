namespace Shelter\{{ Str::studly($name) }}\Providers;

use Shelter\{{ Str::studly($name) }}\Models\{{ Str::singular(Str::studly($name)) }};
use Shelter\{{ Str::studly($name) }}\Models\Control\{{ Str::singular(Str::studly($name)) }} as Control{{ Str::singular(Str::studly($name)) }};
use Shelter\{{ Str::studly($name) }}\Models\Front\{{ Str::singular(Str::studly($name)) }} as Front{{ Str::singular(Str::studly($name)) }};
use Shelter\Kernel\Support\AbstractServiceProvider;

/**
 * Class {{ Str::studly($name) }}ServiceProvider
 * @verbatim@package@endverbatim Shelter\{{ Str::studly($name) }}\Providers
 */
class {{ Str::studly($name) }}ServiceProvider extends AbstractServiceProvider
{
    /**
     * @verbatim@return@endverbatim array
     */
    public static function modelsMorphMap(): array
    {
        return [
            {{ Str::singular(Str::studly($name)) }}::morphMapType() => [
                'control' => Control{{ Str::singular(Str::studly($name)) }}::class,
                'front' => Front{{ Str::singular(Str::studly($name)) }}::class,
            ],
        ];
    }

    /**
     * @verbatim@return@endverbatim void
     */
    public function registerControl(): void
    {
        //
    }

    /**
     * @verbatim@return@endverbatim void
     */
    public function registerFront(): void
    {
        //
    }

    /**
     * @verbatim@return@endverbatim void
     */
    public function registerGlobal(): void
    {
        //
    }

    /**
     * @verbatim@return@endverbatim void
     */
    public function bootControl(): void
    {
        //
    }

    /**
     * @verbatim@return@endverbatim void
     */
    public function bootFront(): void
    {
        //
    }

    /**
     * @verbatim@return@endverbatim void
     */
    public function bootGlobal(): void
    {
        //
    }
}
