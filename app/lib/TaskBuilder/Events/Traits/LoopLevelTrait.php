<?php

namespace App\Lib\TaskBuilder\Events\Traits;

use LogicException;

// ほぼ LoopAttr 用
trait LoopLevelTrait
{
    private $loopLevel = 0; // LoopAttr 1回につき 2 進む

    public function getLoopLevel()
    {
        return $this->loopLevel; // 切り捨て
    }

    public function addLoopLevel()
    {
        $this->loopLevel ++;
    }

    public function subLoopLevel()
    {
        $this->loopLevel --;
        if ($this->loopLevel < 0) {
            throw new LogicException('Nest level is less than 0');
        }
    }

    public function goDownLoopLevel(callable $func, ...$args)
    {
        $this->addLoopLevel();
        try {
            $res = call_user_func($func, ...$args);
            return $res;
        } finally {
            $this->subLoopLevel();
        }
    }
}
