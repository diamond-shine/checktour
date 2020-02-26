<?php

namespace Shelter\Kernel\Injections\ModelRelations;

use Illuminate\Support\Str;
use RuntimeException;
use Shelter;
use Shelter\Kernel\Database\AbstractModel;

trait HotRelationsTrait
{
    /**
     * @var array
     */
    protected static $hotRelations = [];

    /**
     * @param InjectionInterface $injection
     */
    public static function registerModelRelationsInjection(InjectionInterface $injection): void
    {
        self::$hotRelations = \array_merge(
            self::$hotRelations,
            $injection->relations()
        );
    }

    /**
     * @param string $modelClass
     * @return string
     */
    public function resolveModelByContext(string $modelClass): string
    {
        if (! \is_subclass_of($modelClass, AbstractModel::class)) {
            throw new RuntimeException(
                "Model [{$modelClass}] must be subclass of [" . AbstractModel::class . ']'
            );
        }

        if (! isset($modelClass::$variants)) {
            throw new RuntimeException("Model [{$modelClass}] not defined any context models");
        }

        if (! isset(static::$context)) {
            throw new RuntimeException('Current model [' . static::class . '] does not have any context defined');
        }

        if (! isset($modelClass::$variants[static::$context])) {
            throw new RuntimeException(
                "Model [{$modelClass}] does not have variant for [" . static::$context . '] context'
            );
        }

        return $modelClass::$variants[static::$context];
    }

    /**
     * @param string $context
     * @return string
     */
    public static function contextModel(string $context = null): string
    {
        $context = $context ?? Shelter::context();

        if (! isset(static::$variants)) {
            throw new RuntimeException('Model [' . static::class . '] not defined any context models');
        }

        if (! isset(static::$variants[$context])) {
            throw new RuntimeException(
                'Model [' . static::class . '] does not have variant for [' . $context . '] context'
            );
        }

        return static::$variants[$context];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'make') && Str::endsWith($name, 'Relation')) {
            $matches = [];
            \preg_match('/make(\w+)Relation/', $name, $matches);

            $relation = Str::camel($matches[1]);

            if (! isset(self::$hotRelations[$relation])) {
                throw new \RuntimeException("Hot relation with name [{$relation}] not defined");
            }

            return \call_user_func(self::$hotRelations[$relation], $this, ...$arguments);
        }

        return parent::__call($name, $arguments);
    }
}
