<?php

namespace App\Lib\Tasks\Events;

use Illuminate\Database\Eloquent\Collection;

class EventAttrs extends Collection
{
    public static function create(EventAttrs $attrs = null)
    {
        return new self($attrs);
    }

    public function __set($key, $value)
    {
        $this->put($key, $value);
    }

    public function __get($key)
    {
        return $this->get($key);
    }
}
