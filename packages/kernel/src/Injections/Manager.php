<?php

namespace Shelter\Kernel\Injections;

use LogicException;
use Shelter\Kernel\Database\AbstractModel;
use Shelter\Kernel\Injections\ModelRelations\InjectionInterface as ModelRelationsInjectionInterface;

class Manager
{
    /**
     * @param array ...$injections
     * @throws LogicException
     */
    public function inject(...$injections): void
    {
        foreach ($injections as $injection) {
            $this->processInjection($injection);
        }
    }

    /**
     * @param $injection
     * @throws LogicException
     */
    protected function processInjection($injection): void
    {
        if ($injection instanceof ModelRelationsInjectionInterface) {
            $this->addRelationsToModel($injection);
        }
    }

    /**
     * @param ModelRelationsInjectionInterface $injection
     */
    protected function addRelationsToModel(ModelRelationsInjectionInterface $injection): void
    {
        AbstractModel::registerModelRelationsInjection($injection);
    }
}
