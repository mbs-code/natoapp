<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\Events\TaskEventer;

// !!! lib内部実行用
class HandleTaskBuilder extends TaskBuilder
{
    // key は loop のキーとかを入れる用
    public function handle($value, TaskEventer $eventer, $key = null)
    {
        $res = $this->task->handle($value, $eventer, $key);
        return $res;
    }
}
