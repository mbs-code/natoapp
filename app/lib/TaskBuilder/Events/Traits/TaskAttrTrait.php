<?php

namespace App\Lib\TaskBuilder\Events\Traits;

use App\Lib\TaskBuilder\Attrs\BaseAttr;

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
        if ($last !== null) {
            return $this->attrs[$last];
        }
        return null;
    }

    public function getTaskName()
    {
        $attr = $this->getTaskAttr();
        if ($attr) {
            return $attr->getName();
        }
        return null;
    }

    public function getTaskNestLevel()
    {
        return count($this->attrs);
    }
}
