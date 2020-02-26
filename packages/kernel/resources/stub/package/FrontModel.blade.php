namespace Shelter\{{ Str::studly($packageName) }}\Models\Front;

use Shelter\Kernel\Database\FrontModel;
use Shelter\{{ Str::studly($packageName) }}\Models\{{ $modelName }} as Base{{ $modelName }};

/**
 * Class {{ $modelName }}
 * @verbatim@package@endverbatim Shelter\{{ Str::studly($packageName) }}\Models\Front
 *
 * @verbatim@inheritdoc@endverbatim
 */
class {{ $modelName }} extends Base{{ $modelName }}
{
    use FrontModel;
}
