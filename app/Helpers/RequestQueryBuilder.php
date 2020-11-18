<?php

namespace App\Helpers;

use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Http\Request;

class RequestQueryBuilder extends ModelQueryBuilder
{
    protected ?Request $request = null;

    public static function requestBuilder(Builder $query, Request $request)
    {
        $inst = new static($query, $request);
        $inst->request = $request;
        return $inst;
    }


    protected function __construct(Builder $query)
    {
        parent::__construct($query);
    }

    ///
    // inner helper

    protected function getQueryValue(string $queryKey, $default = null)
    {
        $request = $this->request;
        $value = $request
            ? $request->query($queryKey, $default)
            : null;
        return $value;
    }

    /// ////////////////////////////////////////

    public function paginate($queryKey = null)
    {
        $value = $this->getQueryValue($queryKey);
        return parent::paginate($value);
    }

    ///

    public function ifWhereEqual(string $dbKey, $queryKey = null, callable $translate = null)
    {
        $value = $this->getQueryValue($queryKey ?? $dbKey);
        $this->whereEqual($dbKey, $value, $translate);
        return $this;
    }

    public function ifWhereEqualIn(string $dbKey, $queryKey = null, callable $translate = null)
    {
        $value = $this->getQueryValue($queryKey ?? $dbKey);
        $this->whereEqualIn($dbKey, $value, $translate);
        return $this;
    }

    public function ifWhereHasMorphs(string $dbKey, array $morphs = [], $queryKey = null, callable $translate = null)
    {
        $value = $this->getQueryValue($queryKey ?? $dbKey);
        $this->whereHasMorphs($dbKey, $morphs, $value, $translate);
        return $this;
    }

    ///

    public function ifOrderBy(string $sortDbKey = null, $orderKey = null)
    {
        $sort = $this->getQueryValue($sortDbKey);
        $order = $this->getQueryValue($orderKey);
        if ($sort) {
            $this->orderBy($sort, $order);
        }
        return $this;
    }
}
