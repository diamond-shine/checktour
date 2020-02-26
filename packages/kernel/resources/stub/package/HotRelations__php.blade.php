namespace Shelter\{{ Str::studly($packageName) }}\HotRelations;

use Closure;
use Shelter\Kernel\Database\AbstractModel;
use Shelter\Kernel\Injections\ModelRelations\InjectionInterface;

class {{ $baseName }} implements InjectionInterface
{
    /**
     * @verbatim@return@endverbatim Closure[]
     */
    public function relations(): array
    {
        return [
            'relation_name' => function (AbstractModel $context, string ${{ Str::camel($packageName) }}Class) {
                return $context->belongsToMany(${{ Str::camel($packageName) }}Class);
            },
        ];
    }
}
