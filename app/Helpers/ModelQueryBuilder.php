<?php

namespace App\Helpers;

use \Illuminate\Database\Eloquent\Builder;

class ModelQueryBuilder
{
    protected Builder $query;

    public static function builder(Builder $query)
    {
        return new static($query);
    }

    protected function __construct(Builder $query)
    {
        $this->query = $query;
    }

    ///

    public function get()
    {
        return $this->query->get();
    }

    public function first()
    {
        return $this->query->first();
    }

    public function count()
    {
        return $this->query->count();
    }

    public function paginate($perPage = null)
    {
        return $this->query->paginate($perPage);
    }

    public function query()
    {
        return $this->query;
    }

    /// ////////////////////////////////////////

    public function whereEqual(string $dbKey, $value = null, callable $translate = null)
    {
        foreach (collect($value) as $item) {
            $transItem = $translate ? $translate($item) : $item;
            $this->query->where([$dbKey => $transItem]);
        }
        return $this;
    }

    public function whereEqualIn(string $dbKey, $value = null, callable $translate = null)
    {
        if ($value) {
            $items = collect($value);
            $transItems = $translate ? $translate($items) : $items;
            $this->query->whereIn($dbKey, $transItems->toArray());
        }
        return $this;
    }

    public function whereHasMorphs(string $dbKey, array $morphs = [], $value = null, callable $translate = null)
    {
        // ['App\Models\Youtube'],
        if ($value && $morphs) {
            $items = collect($value);
            $this->query->whereHasMorph(
                $dbKey,
                $morphs,
                function (Builder $query) use ($items, $translate) {
                    $transItems = $translate ? $translate($items) : $items;
                    $query->whereIn('id', $transItems->toArray());
                }
            );
        }
        return $this;
    }

    ///

    public function orderBy(string $sortDbKey = null, $order = null)
    {
        if ($sortDbKey) {
            $this->query->orderBy($sortDbKey, $order);
        }
        return $this;
    }
}
