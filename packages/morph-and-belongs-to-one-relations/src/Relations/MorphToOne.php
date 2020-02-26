<?php

namespace Ideil\MorphAndBelongsToOneRelations\Relations;

use Illuminate\Database\Eloquent\{
    Collection,
    Model,
    Relations\MorphToMany
};

class MorphToOne extends MorphToMany
{
    /**
     * @return Model|null
     */
    public function getResults()
    {
        return $this->query->first();
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, null);
        }

        return $models;
    }

    /**
     * @param array $models
     * @param Collection $results
     * @param string $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->{$this->parentKey}])) {
                $model->setRelation(
                    $relation,
                    head($dictionary[$key])
                );
            }
        }

        return $models;
    }
}
