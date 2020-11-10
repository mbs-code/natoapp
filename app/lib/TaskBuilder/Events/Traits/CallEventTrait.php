<?php

namespace App\Lib\TaskBuilder\Events\Traits;
use App\Lib\TaskBuilder\Events\EventRecord;

trait CallEventTrait
{
    private $events = [];

    public function addEvent(string $key, callable $fireFunc)
    {
        $fires = $this->events[$key] ?? [];
        $fires[] = $fireFunc;
        $this->events[$key] = $fires;
        return $this;
    }

    public function addEvents(iterable $items)
    {
        foreach ($items as $key => $item) {
            $this->addEvent($key, $item);
        }
        return $this;
    }

    protected function callEvent(string $key, $value, EventRecord $e, callable $defaultFireFunc = null)
    {
        $fireFuncs = $this->events[$key] ?? [];
        if (count($fireFuncs) > 0) {
            foreach ($fireFuncs as $fireFunc) {
                call_user_func($fireFunc, $value, $e);
            }
        } else if ($defaultFireFunc) {
            call_user_func($defaultFireFunc, $value, $e);
        }
    }
}
