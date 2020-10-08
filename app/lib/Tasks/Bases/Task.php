<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\SingletonTrait;
use App\Lib\EventTrait;

abstract class Task
{
    use SingletonTrait;
    use EventTrait;

    protected abstract function handle($data);

    public static function run($var)
    {
        $instance = self::getInstance();
        $res = $instance->exec($var);
        return $res;
    }

    public function exec($var)
    {
        $res = $this->handle($var);
        return $res;
    }
}
