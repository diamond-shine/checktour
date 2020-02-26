<?php

namespace Ideil\LaravelFileManager\Models;

use Closure;

trait ExtendableModelTrait
{
    /**
     * @var array
     */
    public static $modelExtensions = [
        'methods' => [],
        'relations' => [],
    ];

    /**
     * @var array
     */
    public $injectedModelExtensions = [
        'methods' => [],
        'relations' => [],
    ];

    /**
     * @param string $name
     * @param Closure $handler
     */
    public static function extendByMethod(string $name, Closure $handler): void
    {
        static::$modelExtensions['methods'][$name] = $handler;
    }

    /**
     * @param string $name
     * @param Closure $handler
     */
    public static function extendByRelation(string $name, Closure $handler): void
    {
        static::$modelExtensions['relations'][$name] = $handler;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset(static::$modelExtensions['methods'][$name])) {
            return \call_user_func_array(
                $this->resolveModelExtension('methods', $name),
                $arguments
            );
        }

        if (isset(static::$modelExtensions['relations'][$name])) {
            return \call_user_func_array(
                $this->resolveModelExtension('relations', $name),
                $arguments
            );
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getRelationValue($key)
    {
        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        if (isset(static::$modelExtensions['relations'][$key])
            || method_exists($this, $key)
        ) {
            return $this->getRelationshipFromMethod($key);
        }

        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }
    }

    /**
     * @param string $type
     * @param string $name
     * @return mixed
     */
    public function resolveModelExtension(string $type, string $name)
    {
        if (! isset($this->injectedModelExtensions[$type][$name])) {
            $this->injectedModelExtensions[$type][$name] = Closure::bind(
                static::$modelExtensions[$type][$name],
                $this
            );
        }

        return $this->injectedModelExtensions[$type][$name];
    }
}
