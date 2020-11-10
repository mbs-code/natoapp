<?php

namespace App\Lib\TaskBuilder\Events\Traits;

use LogicException;

trait NestLevelTrait
{
    private $nestLevel = 0.0; // LoopAttr 1回につき 0.5 x 2 進む

    public function getLoopLevel()
    {
        return floor($this->nestLevel); // 切り捨て
    }

    public function getNestLevel()
    {
        return floor($this->nestLevel * 2); // 2倍
    }

    public function goDownNestLevel()
    {
        $this->nestLevel += 0.5;
    }

    public function goUpNestLevel()
    {
        $this->nestLevel -= 0.5;
        if ($this->nestLevel < 0) {
            throw new LogicException('Nest level is less than 0');
        }
    }
}
