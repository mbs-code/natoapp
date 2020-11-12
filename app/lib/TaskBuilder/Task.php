<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\Events\TaskEventer;
use Illuminate\Support\Collection;

abstract class Task
{
    private $builder;

    protected abstract function taskFlow(TaskBuilder $builder): TaskBuilder;

    public static function run($value, TaskEventer $e = null)
    {
        $instance = new static();
        return $instance->exec($value, $e);
    }

    ///

    public function exec($value, TaskEventer $e = null)
    {
        $builder = $this->getBuilder();
        return $builder->exec($value, $e);
    }

    public function getBuilder()
    {
        return $this->builder ?? $this->taskFlow(TaskBuilder::builder());
    }

    ///

    protected function collect()
    {
        return function($val) {
            return collect($val);
        };
    }

    protected function chunk(int $size)
    {
        return function(Collection $val) use ($size) {
            return $val->chunk($size);
        };
    }

    protected function flatten(int $depth = 1)
    {
        return function(Collection $val) use ($depth) {
            return $val->flatten($depth);
        };
    }
}
