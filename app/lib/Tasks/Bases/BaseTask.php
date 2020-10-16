<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\SingletonTrait;
use App\Lib\Tasks\Events\EventTrait;
use App\Lib\Tasks\Events\EventAttrs;
use LogicException;

/**
 * preFormat() -> handle() -> postFormat()
 *
 * <event> attribute:
 * - <beforeExec>: exec 前処理
 * - <beforeProcess>: process 前処理、preFormat 実行後
 * - <processed>: process 後処理
 * - <execed>: exec 後処理、postFormat 実行後
 *   - execProps: process 前の値
 *   - processProps: process 前の値
 *   - processResponse: process 後の値
 *   - execResponse: exec 後の値
 *
 *   - startTimestamp: 開始時刻
 */
abstract class BaseTask
{
    use SingletonTrait;
    use EventTrait;

    private $e;

    public abstract function exec($var);

    public static function builder()
    {
        return self::getInstance(true);
    }

    public static function run($var)
    {
        $instance = self::builder();
        $res = $instance->exec($var);
        return $res;
    }

    /// ////////////////////////////////////////

    protected function bindEventAttrs()
    {
        $this->e = new EventAttrs();
        return $this->e;
    }

    protected function unbindEventAttrs()
    {
        unset($this->e); // メモリ解放
        $this->e = null;
    }

    protected function getEventAttrs()
    {
        $attrs = $this->e;
        if (!$attrs) {
            throw new LogicException('Call $this->bindEventAttrs()');
        }

        return $this->e;
    }

    protected function getEventAttr(string $key = null, $default = null)
    {
        return $this->getEventAttrs()->get($key, $default);
    }

    protected function setEventAttr(string $key, $value)
    {
        $this->getEventAttrs()->put($key, $value);
    }
}
