<?php

namespace App\Lib\TaskBuilder\Events\Traits;

use App\Lib\TaskBuilder\Attrs\BaseAttr;
use LogicException;

trait TaskAttrTrait
{
    private $attrs = []; // task attr stack

    public function pushTaskAttr(BaseAttr $attr)
    {
        array_push($this->attrs, $attr);
    }

    public function popTaskAttr()
    {
        $attr = array_pop($this->attrs);
        return $attr;
    }

    public function getTaskAttr()
    {
        // 現在のを取り出す
        $last = array_key_last($this->attrs);
        $attr = $this->attrs[$last];

        if (!$attr) {
            new LogicException('Event attr array is Empty. use pushEventAttr()');
        }
        return $attr;
    }

    public function getTaskName()
    {
        $attr = $this->getTaskAttr();
        return $attr->getName();
    }

    public function getTaskNestLevel()
    {
        return count($this->attrs);
    }
}
