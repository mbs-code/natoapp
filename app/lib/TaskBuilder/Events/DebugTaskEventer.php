<?php

namespace App\Lib\TaskBuilder\Events;

use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\Jobs\BaseJob;
use App\Lib\TaskBuilder\Utils\ConsoleColor;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;
use Exception;

class DebugTaskEventer extends TaskEventer
{
    public $indent = '  ';
    public $length = 100;

    public $colorEvents = [
        SGR::COLOR_FG_PURPLE, SGR::COLOR_FG_GREEN, SGR::COLOR_FG_CYAN,
    ];

    public $colorValue = SGR::COLOR_FG_WHITE;
    public $colorNullValue = SGR::COLOR_FG_YELLOW;
    public $colorExceptionValue = SGR::COLOR_FG_YELLOW;

    public $colorSynbolAddRecord = SGR::COLOR_FG_YELLOW;
    public $colorSynbolRemoveRecord = SGR::COLOR_FG_RED;
    public $colorRecord = SGR::COLOR_FG_BLACK_BRIGHT;

    public $colorSymbolPushJob = SGR::COLOR_FG_BLACK_BRIGHT;
    public $colorSymbolPopJob = SGR::COLOR_FG_BLACK_BRIGHT;
    public $colorJob = SGR::COLOR_FG_BLACK_BRIGHT;
    ///

    private $console; // color console

    function __construct()
    {
        parent::__construct();
        $this->console = new ConsoleColor();
        $this->console->setLineLength($this->length);
    }

    public function fireEvent(string $shortKey, $value, callable $defaultFireFunc = null)
    {
        $eventName = parent::fireEvent($shortKey, $value, $defaultFireFunc);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getLoopLevel());
        $count = $this->record->get($eventName);
        $this->console
            // ->color(SGR::COLOR_FG_CYAN)
            ->color($this->getEventColor($this->getJobNestLevel()))
            ->text("{$padding}[{$count}] <{$eventName}>: ")
            ->reset();

        if ($value instanceof Exception) {
            $this->console->color($this->colorExceptionValue);
        } else if ($value === null) {
            $this->console->color($this->colorNullValue);
        }

        $this->console
            ->color($this->colorValue)
            ->print($value, true)
            ->br();
    }

    ///

    public function incrementRecord(string $shortKey)
    {
        $eventName = parent::incrementRecord($shortKey);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getLoopLevel()); // スペース
        $count = $this->record->get($eventName);
        $this->console
            ->color($this->colorSynbolAddRecord)
            ->text("{$padding}=> ")
            ->color($this->colorRecord)
            ->text("{$eventName}: ")
            ->print($count)
            ->br();

        return $eventName;
    }

    public function writeRecord(string $shortKey, $value)
    {
        $eventName = parent::writeRecord($shortKey, $value);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getLoopLevel()); // スペース
        $this->console
            ->color($this->colorSynbolAddRecord)
            ->text("{$padding}=> ")
            ->color($this->colorRecord)
            ->text("{$eventName}: ")
            ->print($value, true)
            ->br();

        return $eventName;
    }

    public function clearRecords($shortKey)
    {
        $eventNames = parent::clearRecords($shortKey);

        // ダンプ
        $padding = str_repeat($this->indent, $this->getLoopLevel()); // スペース
        $this->console
            ->color($this->colorSynbolRemoveRecord)
            ->text("{$padding}!! ")
            ->color($this->colorRecord)
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
            ->color($this->colorSymbolPushJob)
            ->text(">> push({$this->getJobNestLevel()}): ")
            ->color($this->colorJob)
            ->print($jobName, true)
            ->br();
    }

    public function popEventJob()
    {
        $job = parent::popEventJob();

        // ダンプ
        $jobName = $this->jobToName($job);
        $this->console
            ->color($this->colorSymbolPopJob)
            ->text(">> pull({$this->getJobNestLevel()}): ")
            ->color($this->colorJob)
            ->print($jobName, true)
            ->br();

        return $job;
    }

    ///

    private function getEventColor(int $nest)
    {
        $len = count($this->colorEvents);
        return $this->colorEvents[$nest % $len];
    }

    private function jobToName(BaseJob $job)
    {
        return class_basename($job).'@'.$job->getName();
    }

}
