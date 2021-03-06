<?php

namespace App\Sharp\Filters;

use App\SpaceshipType;
use Code16\Sharp\EntityList\EntityListRequiredFilter;

class SpaceshipTypeFilter implements EntityListRequiredFilter
{

    public function values()
    {
        return SpaceshipType::orderBy("label")
            ->pluck("label", "id");
    }

    public function defaultValue()
    {
        return SpaceshipType::orderBy("label")->first()->id;
    }
}