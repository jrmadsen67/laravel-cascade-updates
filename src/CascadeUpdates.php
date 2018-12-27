<?php
namespace jrmadsen67\Database\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LogicException;

trait CascadeUpdates
{

    public $updateCascadeActive = true;

    /**
     * Boot the trait.
     *
     * Listen for the updating event of a model, and run
     * the update operation for any configured relationship methods.
     *
     * @throws \LogicException
     */
    protected static function bootCascadeUpdates()
    {
        static::updating(function ($model) {
            if ($invalidCascadingRelationships = $model->hasInvalidCascadingRelationships()) {
                throw new LogicException(sprintf(
                    '%s [%s] must exist and return an object of type Illuminate\Database\Eloquent\Relations\Relation',
                    str_plural('Relationship', count($invalidCascadingRelationships)),
                    join(', ', $invalidCascadingRelationships)
                ));
            }

            foreach ($model->getActiveCascadingUpdates() as $relationship) {
                if ($model->{$relationship} instanceof Model) {
                    $model->{$relationship}->update($model->hydrateUpdate($relationship)); //@TODO: test this
                } else {
                    foreach ($model->{$relationship} as $child) {
                        $child->update($model->hydrateUpdate($relationship));
                    }
                }
            }

        });
    }


    /**
     * Determine if the current model has any invalid cascading relationships defined.
     *
     * A relationship is considered invalid when the method does not exist, or the relationship
     * method does not return an instance of Illuminate\Database\Eloquent\Relations\Relation.
     *
     * @return array
     */
    protected function hasInvalidCascadingRelationships()
    {
        return array_filter($this->getCascadingUpdates(), function ($relationship) {
            return ! method_exists($this, $relationship) || ! $this->{$relationship}() instanceof Relation;
        });
    }

    /**
     * Fetch the defined cascading updates for this model.
     *
     * @return array
     */
    protected function getCascadingUpdates()
    {
        return isset($this->cascadeUpdates) ? (array) array_keys($this->cascadeUpdates) : [];
    }

    /**
     * For the cascading updates defined on the model, return only those that are not null.
     *
     * @return array
     */
    protected function getActiveCascadingUpdates()
    {
        return array_filter($this->getCascadingUpdates(), function ($relationship) {
            return ! is_null($this->{$relationship});
        });
    }


    /**
     * @return array
     */
    function hydrateUpdate($relationship): array{
        $fieldsForUpdate = $this->cascadeUpdates[$relationship];
        return array_only($this->attributes, $fieldsForUpdate);
    }

    // @TODO
    public function turnOffCascade(){
        $this->updateCascadeActive = true;
        return $this;
    }
}