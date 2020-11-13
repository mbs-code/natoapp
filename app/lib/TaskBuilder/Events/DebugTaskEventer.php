<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\Jobs\BaseJob;
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
            ->color($this->getEventColor($this->getJobNestLevel()))
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
    // TaskJobTrait の拡張

    public function pushEventJob(BaseJob $job)
    {
        parent::pushEventJob($job);

        // ダンプ
        $jobName = $this->jobToName($job);
        $this->console
            ->color(SGR::COLOR_FG_BLACK_BRIGHT)
            ->text(">> push({$this->getJobNestLevel()}): ")
            ->print($jobName, true)
            ->br();
    }

    public function popEventJob()
    {
        $job = parent::popEventJob();

        // ダンプ
        $jobName = $this->jobToName($job);
        $this->console
            ->color(SGR::COLOR_FG_BLACK_BRIGHT)
            ->text(">> pull({$this->getJobNestLevel()}): ")
            ->print($jobName, true)
            ->br();

        return $job;
    }

    ///

    private function getEventColor(int $nest)
    {
        $len = count(self::$eventColors);
        return self::$eventColors[$nest % $len];
    }

    private function jobToName(BaseJob $job)
    {
        return class_basename($job).'@'.$job->getName();
    }

}
