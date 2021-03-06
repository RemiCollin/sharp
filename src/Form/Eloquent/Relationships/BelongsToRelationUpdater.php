<?php

namespace Code16\Sharp\Form\Eloquent\Relationships;

class BelongsToRelationUpdater
{

    /**
     * @param $instance
     * @param string $attribute
     * @param $value
     */
    public function update($instance, $attribute, $value)
    {
        if(strpos($attribute, ":") !== false) {
            // This is a relation attribute update case (eg: mother:name)
            list($attribute, $subAttribute) = explode(":", $attribute);

            if($instance->$attribute) {
                $instance->$attribute()->update([
                    $subAttribute => $value
                ]);

            } elseif($value) {
                $instance->$attribute()->create([
                    $subAttribute => $value
                ]);
            }

            return;
        }

        $instance->$attribute()->associate($value);
        $instance->save();
    }
}