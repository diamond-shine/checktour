<?php

namespace Shelter\Kernel;

use Illuminate\Database\Eloquent\Relations\Relation;

class Kernel
{
    /**
     * @var Application
     */
    public $app;

    /**
     * @var string
     */
    protected static $context;

    /**
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param string $name
     */
    public static function changeContext(string $name): void
    {
        if (! \in_array($name, ['control', 'front'], true)) {
            throw new InvalidArgumentException('Invalid context name ["control", "front"]');
        }

        static::$context = $name;

        Relation::$morphMap = \array_merge(
            Relation::$morphMap ?? [],
            Relation::$morphMap[$name] ?? []
        );
    }

    /**
     * @return string
     */
    public static function context(): string
    {
        if (static::$context === null) {
            if (\is_control_panel()) {
                static::$context = 'control';
            } elseif (\is_front()) {
                static::$context = 'front';
            } else {
                static::$context = \env('APP_DEFAULT_SYSTEM_ENV', 'front');
            }
        }

        return static::$context;
    }
}
