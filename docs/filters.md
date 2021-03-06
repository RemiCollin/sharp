# Entity List Filters

Entity List Filters are simply a way for the user to filter list items on some attribute. Like filter cats on color is black, for instance.


## Write the filter class

First, we need to write a class which implements the `Code16\Sharp\EntityList\EntityListFilter` interface, and therefore declare a `values()` function. This function must return an ["id" => "label"] array. For instance, with Eloquent:

    class SpaceshipTypeFilter implements EntityListRequiredFilter
    {
        public function values()
        {
            return SpaceshipType::orderBy("label")
                ->pluck("label", "id");
        }
    }


## Configure the filter

Next, in the EntityList, we add the filter in the configuration:

    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            [...]
            ->addFilter("type", SpaceshipTypeFilter::class);
    }

Sharp will display a dropdown with those values for a "type" filter.


## Handle filter selection

Once the user clicked on a filter, Sharp will call EntityList's `getListData(EntityListQueryParams $params)` with the filter value in param, which can be grabbed with `$params->filterFor("key")`. For instance:

    function getListData(EntityListQueryParams $params)
    {
        $spaceships = Spaceship::select("spaceships.*")->distinct();

        if($params->filterFor("type")) {
            $spaceships->where("type_id", $params->filterFor("type"));
        }

        [...]
    }


## Required filters

Sometimes we'd like to have a filter which can't be null. Just implement `Code16\Sharp\EntityList\EntityListRequiredFilter` subinterface:

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

You'll have to declare another function, `defaultValue()`, which must return the id of the default filter if nothing selected.


## Multiple filter

First, notice that you can have as many filters as you want for an EntityList. The "multiple filter" here designate something else: allowing the user to select more than one value for a filter. To achieve this, simply replace the interface implemented with `Code16\Sharp\EntityList\EntityListMultipleFilter`, and that's it.

In this case, with Eloquent for instance, your might have to modify your code to ensure that you have an array (Sharp will return either null, and id or an array of id, depending on the user selection):

    if ($params->filterFor("pilots")) {
        $spaceships->whereIn(
            "pilots.id", 
            (array)$params->filterFor("pilots")
        );
    }


Note that a filter can't be required AND multiple.

---

> Next chapter : [Commands](commands.md)