<?php

namespace App\Lib\Tasks\Events;

trait EventTrait
{
    private $events = [];

    public function addEvent(string $key, callable $fireEvent)
    {
        $fires = $this->events[$key] ?? [];
        $fires[] = $fireEvent;
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

    protected function fireEvent(string $key, EventAttrs $e, callable $defaultEvent = null)
    {
        $fires = $this->events[$key] ?? [];
        if (count($fires) > 0) {
        foreach ($fires as $fire) {
            $fire($e);
        }
        } else if ($defaultEvent) {
        $defaultEvent($e);
        }
    }
}
