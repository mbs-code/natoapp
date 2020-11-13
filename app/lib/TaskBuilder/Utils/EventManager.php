<?php

namespace App\Lib\TaskBuilder\Utils;

class EventManager
{
    private $events = [];

    public function addEvent(string $eventName, callable $fireFunc)
    {
        $fires = $this->events[$eventName] ?? [];
        $fires[] = $fireFunc;
        $this->events[$eventName] = $fires;
        return $this;
    }

    public function callEvent(string $eventName, $value, EventRecord $e, callable $defaultFireFunc = null)
    {
        $fireFuncs = $this->events[$eventName] ?? [];
        if (count($fireFuncs) > 0) {
            foreach ($fireFuncs as $fireFunc) {
                call_user_func($fireFunc, $value, $e);
            }
        } else if ($defaultFireFunc) {
            call_user_func($defaultFireFunc, $value, $e);
        }
    }
}
