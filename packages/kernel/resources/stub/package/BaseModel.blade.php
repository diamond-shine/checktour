namespace Shelter\{{ Str::studly($packageName) }}\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Shelter\Kernel\Database\AbstractUUIDModel;
use Carbon\Carbon;

/**
 * Class {{ $modelName }}
 * @verbatim@package@endverbatim Shelter\{{ Str::studly($packageName) }}\Models
 *
 * @verbatim@property@endverbatim string $id
 * @verbatim@property@endverbatim Carbon|null $deleted_at
 * @verbatim@property@endverbatim Carbon $created_at
 * @verbatim@property@endverbatim Carbon $updated_at
 */
abstract class {{ $modelName }} extends AbstractUUIDModel
{
    use SoftDeletes;

    /**
     * @verbatim@var@endverbatim array
     */
    protected static $variants = [
        'control' => Control\{{ $modelName }}::class,
        'front' => Front\{{ $modelName }}::class,
    ];

    /**
     * @verbatim@var@endverbatim array
     */
    protected $guarded = [
        'id',
    ];
}
