<?php

namespace App\Lib;

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

  protected function fireEvent(string $key, ...$params)
  {
    $fires = $this->events[$key] ?? [];
    foreach ($fires as $fire) {
      $fire(...$params);
    }
  }
}
