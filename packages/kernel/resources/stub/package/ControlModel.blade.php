namespace Shelter\{{ Str::studly($packageName) }}\Models\Control;

use Shelter\Kernel\Database\ControlModel;
use Shelter\{{ Str::studly($packageName) }}\Models\{{ $modelName }} as Base{{ $modelName }};

/**
 * Class {{ $modelName }}
 * @verbatim@package@endverbatim Shelter\{{ Str::studly($packageName) }}\Models\Control
 *
 * @verbatim@inheritdoc@endverbatim
 */
class {{ $modelName }} extends Base{{ $modelName }}
{
    use ControlModel;
}
