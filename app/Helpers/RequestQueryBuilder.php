<?php

namespace App\Helpers;

use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Http\Request;

class RequestQueryBuilder
{

    protected Builder $query;
    protected Request $request;

    public static function builder(Builder $query, Request $request = null)
    {
        // $class = get_called_class();
        // return new $class($query, $request);
        return new static($query, $request);
    }

    private function __construct(Builder $query, Request $request = null)
    {
        $this->query = $query;
        $this->request = $request;
    }

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

    public function paginate(string $perPageKey, $default = null)
    {
        $perPage = $this->q($perPageKey, $default);
        return $this->query->paginate($perPage);
    }

    public function query()
    {
        return $this->query;
    }

    /// ////////////////////////////////////////

    protected function q(string $key, $default = null)
    {
        $req = $this->request;
        return $req ? $req->query($key, $default) : null;
    }

    protected function qa(string $key, $default = null)
    {
        $val = $this->q($key, $default);
        return $val ? (is_array($val) ? $val : [$val]) : null;
    }

    /// ////////////////////////////////////////

    /**
     * $key が存在するなら、全てと一致するものを抽出.
     */
    public function ifWhereEqual(string $key, callable $translate = null)
    {
        if ($ary = $this->qa($key)) {
            foreach ($ary as $x) {
                $val = $translate ? $translate($x) : $x;
                $this->query->where([$key => $val]);
            }
        }
        return $this;
    }

    /**
     * $key が存在するなら、どれかに一致するものを抽出.
     */
    public function ifWhereEqualIn(string $key, callable $translate = null)
    {
        if ($ary = $this->qa($key)) {
            $this->query->where(function ($query) use ($key, $ary, $translate) {
                foreach ($ary as $x) {
                    $val = $translate ? $translate($x) : $x;
                    $query->orWhere([$key => $val]);
                }
            });
        }
        return $this;
    }

    /// ////////////////////////////////////////

    /**
     * $key が存在するなら、どれかが morph に一致するものを抽出.
     */
    public function ifWhereHasMorph(string $key, callable $translate = null)
    {
        if ($ary = $this->qa($key)) {
            $this->query->whereHasMorph(
                $key,
                ['App\Models\Youtube'],
                function (Builder $query) use ($key, $ary, $translate) {
                    $query->where(function ($query) use ($key, $ary, $translate) {
                        foreach ($ary as $x) {
                            $val = $translate ? $translate($x) : $x;
                            $query->orWhere(['id' => $val]);
                        }
                    });
                }
            );
        }
        return $this;
    }

    /// ////////////////////////////////////////

    public function ifOrderBy(string $sortKey, string $orderKey)
    {
        if ($sort = $this->q($sortKey)) {
            $order = $this->q($orderKey, 'asc');
            $this->query->orderBy($sort, $order);
        }
        return $this;
    }
}
