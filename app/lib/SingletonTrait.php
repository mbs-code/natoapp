<?php

namespace App\Lib;

// ref: https://qiita.com/YusukeHigaki/items/36baa45851fd37bf56b8
trait SingletonTrait
{
    private static $instance = [];

    private function __construct()
    {
    }

    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instance[$class])) self::$instance[$class] = new $class;

        return self::$instance[$class];
    }

    public final function __clone()
    {
        throw new \Exception('Clone is not allowed against' . get_class($this));
    }
}
