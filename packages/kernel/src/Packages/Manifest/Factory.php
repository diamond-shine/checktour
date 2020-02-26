<?php

namespace Shelter\Kernel\Packages\Manifest;

/**
 * Class Factory
 * @package Shelter\Kernel\Packages\Manifest
 */
class Factory
{
    /**
     * @var array
     */
    protected $payload = [
        'database' => [
            'migrations' => false,
        ],
        'configs' => [],
        'commands' => [],
        'hot_relations' => [],
    ];

    /**
     * @return $this
     */
    public function hasMigrations(): self
    {
        $this->payload['database']['migrations'] = true;

        return $this;
    }

    /**
     * @param string $from
     * @param string $to
     * @return $this
     */
    public function config(string $from, string $to): self
    {
        $this->payload['configs'][$from] = $to;

        return $this;
    }

    /**
     * @param string $commandClass
     * @return $this
     */
    public function command(string $commandClass): self
    {
        $this->payload['commands'][] = $commandClass;

        return $this;
    }

    /**
     * @param array $commandClasses
     * @return $this
     */
    public function commands(array $commandClasses): self
    {
        $this->payload['commands'] = \array_merge(
            $this->payload['commands'],
            $commandClasses
        );

        return $this;
    }

    /**
     * @param string $hotRelationClass
     * @return $this
     */
    public function hotRelation(string $hotRelationClass): self
    {
        $this->payload['hot_relations'][] = $hotRelationClass;

        return $this;
    }

    /**
     * @param array $hotRelationClass
     * @return $this
     */
    public function hotRelations(array $hotRelationClass): self
    {
        $this->payload['hot_relations'] = \array_merge(
            $this->payload['hot_relations'],
            $hotRelationClass
        );

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->payload;
    }

    /**
     * @return mixed
     */
    public static function make(): Factory
    {
        return new static;
    }
}
