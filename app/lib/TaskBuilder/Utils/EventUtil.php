<?php

namespace App\Lib\TaskBuilder\Utils;

class EventUtil
{
    public static $brackets = [
        ['[', ']'], ['<', '>'], ['{', '}'],
    ];

    public static function allCount($val)
    {
        if (is_countable($val)) {
            return count($val);
        }
        return $val !== null ? 1 : 0;
    }

    ///

    public static function durationString(EventRecord $e)
    {
        $start = $e->get('timestamp');
        $now = microtime(true);
        $sec = $start
            ? number_format($now - $start, 2)
            : '-.--';

        return "{$sec}sec";
    }

    public static function prefString(EventRecord $e, string ...$jobNames)
    {
        $text = '';
        foreach ($jobNames as $index => $jobName) {
            $bracket = static::getBrackets($index);
            $index = $e->getRecordValue('index', $jobName, 0);
            $length = $e->getRecordValue('length', $jobName, 0);
            $pref = "{$bracket[0]}{$index}/{$length}{$bracket[1]}";
            $text .= $pref;
        }

        return $text;
    }

    public static function statString(EventRecord $e, string $jobName)
    {
        $length = $e->getRecordValue('length', $jobName, 0);
        $success = $e->getRecordValue('success', $jobName, 0);
        $skip = $e->getRecordValue('skip', $jobName, 0);
        $throw = $e->getRecordValue('throw', $jobName, 0);

        $rate = $length !== 0
            ? number_format((($success + $skip) / $length) * 100, 2)
            : '--.-'; // number_format(100, 2);

        return "{$rate}%, {$success}+{$skip}/{$length}, skip: {$skip}, throw: {$throw}";
    }

    public static function allStatString(EventRecord $e, string $jobName)
    {
        $length = $e->getEventValue('current', $jobName, 0);
        $success = $e->getEventValue('success', $jobName, 0);
        $skip = $e->getEventValue('skip', $jobName, 0);
        $throw = $e->getEventValue('throw', $jobName, 0);

        $rate = $length !== 0
            ? number_format((($success + $skip) / $length) * 100, 2)
            : '--.-'; // number_format(100, 2);

        return "{$rate}%, {$success}+{$skip}/{$length}, skip: {$skip}, throw: {$throw}";
    }

    ///

    private static function getBrackets(int $num)
    {
        $len = count(static::$brackets);
        return static::$brackets[$num % $len];
    }
}
