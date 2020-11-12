<?php

namespace App\Lib\TaskBuilder\Utils;

use Illuminate\Support\Collection;

class EventRecord extends Collection
{
    public function increment($key)
    {
        $num = $this->get($key, 0) + 1;
        $this->put($key, $num);
        return $num;
    }

    public function unset($key)
    {
        return $this->pull($key);
    }
}
