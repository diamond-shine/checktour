<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

/**
 * @param string $path
 * @return string
 */
function static_asset(string $path): string
{
    $staticUrl = rtrim(
        env('APP_STATIC_URL'),
        '/'
    );

    return $staticUrl . '/' . ltrim($path, '/');
}

/**
 * @param string $text
 * @param mixed ...$args
 * @return string
 */
function _s(string $text, ...$args): string
{
    return _(
        sprintf($text, ...$args)
    );
}

/**
 * @return bool
 */
function is_auth_pages(): bool
{
    $action = trim(
        str_replace(
            control_prefix(),
            '',
            request()->path()
        ),
        '/'
    );

    $pattern = '/^(login|logout|forgot|password\/reset\/\w+|password\/reset)$/';

    return (bool)preg_match($pattern, $action);
}

/**
 * @return bool
 */
function is_control_panel(): bool
{
    return ! is_console()
        && Str::startsWith(
            request()->path(),
            control_prefix()
        );
}

/**
 * @return bool
 */
function is_console(): bool
{
    return app()->runningInConsole();
}

/**
 * @return bool
 */
function is_front()
{
    return ! is_console() && ! is_control_panel();
}

/**
 * @return string
 */
function control_prefix(): string
{
    static $prefix;

    if ($prefix === null) {
        $prefix = trim(
            env('APP_CONTROL_PREFIX', 'control'),
            '\\'
        );
    }

    return $prefix;
}

/**
 * @param string|object $class
 * @param string $method
 * @param array|null $parameters
 * @return string|array
 */
function makeAction($class, string $method, array $parameters = null)
{
    $className = trim(
        \is_object($class) ? get_class($class) : $class,
        '\\'
    );

    $action = "\\{$className}@{$method}";

    return $parameters === null ? $action : [$action, $parameters];
}

if (! function_exists('array_merge_recursive_distinct')) {
    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    function array_merge_recursive_distinct(array &$array1, array &$array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }
}

/**
 * @param string|\Illuminate\Database\Eloquent\Builder $query
 * @param array $params
 * @return string
 */
function interpolateQuery($query, array $params = []): string
{
    if (! is_string($query)) {
        $params = $query->getBindings();
        $query = $query->toSql();
    }

    return array_reduce($params, function (string $query, $param) {
        return substr_replace(
            $query,
            is_numeric($param) ? $param : "'{$param}'",
            strpos($query, '?'),
            1
        );
    }, $query);
}

/**
 * @param $value
 * @param array $allowedValues
 * @param null|mixed $default
 * @param bool $strict
 * @return mixed|null
 */
function array_filter_by($value, array $allowedValues = [], $default = null, bool $strict = true)
{
    return \in_array($value, $allowedValues, $strict) ? $value : $default;
}

if (! function_exists('_')) {
    function _(string $text)
    {
        return $text;
    }
}


/**
 * @param $path
 */
function init_workbench($path): void
{
    $finder = new Finder;
    $files = new Filesystem;

    $autoload = $finder->in($path)->files()->name('autoload.php')->depth('<= 3')->followLinks();

    foreach ($autoload as $file) {
        $files->requireOnce($file->getRealPath());
    }
}

/**
 * @param string|array $key
 * @param array $data
 * @param bool $nested
 * @return array
 */
function wrap_gettext($key, array $data, bool $nested = false): array
{
    if (! $data) {
        return $data;
    }

    if (! $nested) {
        Arr::set(
            $data,
            $key,
            _(Arr::get($data, $key))
        );

        return $data;
    }

    $keys = array_keys($data);

    while ($data[$i = array_shift($keys)] ?? null) {
        Arr::set(
            $data,
            "{$i}.$key",
            _(Arr::get($data, "{$i}.$key"))
        );
    }

    return $data;
}

/**
 * @param string $str
 * @return string
 */
function real_snake_case(string $str): string
{
    return str_replace(
        '-',
        '_',
        Str::snake($str)
    );
}

/**
 * @param array $array
 * @param string $keyAs
 * @param string $valueAs
 * @return array
 */
function array_expand(array $array, string $keyAs = 'key', string $valueAs = 'value'): array
{
    $result = [];

    foreach ($array as $key => $value) {
        $result[] = [
            $keyAs => $key,
            $valueAs => $value,
        ];
    }

    return $result;
}

/**
 * @param mixed $term
 * @param bool $isNotEmpty
 * @return bool
 */
function is_valid_string($term, bool $isNotEmpty = true): bool
{
    $status = \is_string($term) && \json_encode($term) !== false;

    if (! $status && ! $isNotEmpty) {
        return false;
    }

    return \trim($term) !== '';
}

/**
 * @param string $key
 * @return mixed
 */
function envOrFail(string $key)
{
    $value = env($key);

    if ($value === null) {
        throw new InvalidArgumentException("Env key [$key] not defined");
    }

    return $value;
}
