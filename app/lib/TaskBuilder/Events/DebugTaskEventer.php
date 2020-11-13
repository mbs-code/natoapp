<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\Attrs\BaseAttr;
use App\Lib\TaskBuilder\Utils\ConsoleColor;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use Exception;

class DebugTaskEventer extends TaskEventer
{
    public static $indent = '  ';
    public static $length = 100;

    public static $eventColors = [
        SGR::COLOR_FG_PURPLE, SGR::COLOR_FG_GREEN, SGR::COLOR_FG_CYAN,
    ];

    private $console; // color console

    function __construct()
    {
        parent::__construct();
        $this->console = new ConsoleColor();
        $this->console->setLineLength(self::$length);
    }

    public function fireEvent(string $shortKey, $value, callable $defaultFireFunc = null)
    {
        $eventName = parent::fireEvent($shortKey, $value, $defaultFireFunc);

        // ダンプ
        $padding = str_repeat(self::$indent, $this->getLoopLevel());
        $count = $this->record->get($eventName);
        $this->console
            // ->color(SGR::COLOR_FG_CYAN)
            ->color($this->getEventColor($this->getTaskNestLevel()))
            ->text("{$padding}[{$count}] <{$eventName}>: ")
            ->reset();

        if ($value instanceof Exception) {
            $this->console->color(SGR::COLOR_FG_RED);
        } else if ($value === null) {
            $this->console->color(SGR::COLOR_FG_YELLOW);
        }

        $this->console
            ->print($value, true)
            ->br();
    }

    ///

    public function incrementRecord(string $shortKey)
    {
        $eventName = parent::incrementRecord($shortKey);

        // ダンプ
        $padding = str_repeat(self::$indent, $this->getLoopLevel()); // スペース
        $count = $this->record->get($eventName);
        $this->console
            ->color(SGR::COLOR_FG_YELLOW)
            ->text("{$padding}=> ")
            ->color(SGR::COLOR_FG_BLACK_BRIGHT)
            ->text("{$eventName}: ")
            ->print($count)
            ->br();

        return $eventName;
    }

    public function writeRecord(string $shortKey, $value)
    {
        $eventName = parent::writeRecord($shortKey, $value);

        // ダンプ
        $padding = str_repeat(self::$indent, $this->getLoopLevel()); // スペース
        $this->console
            ->color(SGR::COLOR_FG_YELLOW)
            ->text("{$padding}=> ")
            ->color(SGR::COLOR_FG_BLACK_BRIGHT)
            ->text("{$eventName}: ")
            ->print($value, true)
            ->br();

        return $eventName;
    }

    public function clearRecords($shortKey)
    {
        $eventNames = parent::clearRecords($shortKey);

        // ダンプ
        $padding = str_repeat(self::$indent, $this->getLoopLevel()); // スペース
        $this->console
            ->color(SGR::COLOR_FG_RED)
            ->text("{$padding}!! ")
            ->color(SGR::COLOR_FG_BLACK_BRIGHT)
            ->print($eventNames, true)
            ->br();

        return $eventNames;
    }

    ///
    // TaskAttrTrait の拡張

    public function pushEventAttr(BaseAttr $attr)
    {
        parent::pushEventAttr($attr);

        // ダンプ
        $attrName = $this->attrToName($attr);
        $this->console
            ->color(SGR::COLOR_FG_BLACK_BRIGHT)
            ->text(">> push({$this->getTaskNestLevel()}): ")
            ->print($attrName, true)
            ->br();
    }

    public function popEventAttr()
    {
        $attr = parent::popEventAttr();

        // ダンプ
        $attrName = $this->attrToName($attr);
        $this->console
            ->color(SGR::COLOR_FG_BLACK_BRIGHT)
            ->text(">> pull({$this->getTaskNestLevel()}): ")
            ->print($attrName, true)
            ->br();

        return $attr;
    }

    ///

    private function getEventColor(int $nest)
    {
        $len = count(self::$eventColors);
        return self::$eventColors[$nest % $len];
    }

    private function attrToName(BaseAttr $attr)
    {
        return class_basename($attr).'@'.$attr->getName();
    }

}
