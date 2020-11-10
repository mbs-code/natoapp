<?php

namespace App\Lib\TaskBuilder\Events\Traits;

use App\Lib\TaskBuilder\Attrs\BaseAttr;
use LogicException;

trait TaskAttrTrait
{
    private $attr; // 現在の TaskAttr

    public function setEventAttr(BaseAttr $attr)
    {
        $this->attr = $attr;
    }

    public function clearEventAttr()
    {
        $this->attr = null;
    }

    public function getTaskName()
    {
        if (!$this->attr) {
            new LogicException('Use setEventAttr()');
        }
        return $this->attr->getName();
    }
}
