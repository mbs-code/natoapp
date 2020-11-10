<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\Events\TaskEventer;

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
}
