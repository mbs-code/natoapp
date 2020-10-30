<?php

namespace App\Lib\Tasks\Utils;


class GeneralEvents
{
    public static function arrayTaskEvents(string $eventName = null)
    {
        $e = self::core(true);

        $e->put('beforeOuterLoop', function ($e) use ($eventName) {
            $pref = $eventName ?? 'Task';
            $total = self::wrapCount($e->execProps);
            logger()->notice("{$pref} (length: {$e->outerLength}, total: {$total})");
        });

        /// ////////////////////////////////////////

        $e->put('outerLooped', function ($e) use ($eventName) {
            $pref = $eventName ?? 'Task';
            $time = number_format(microtime(true) - $e->startTimestamp, 2);
            $stat = self::rateStatString($e, 'outer');
            logger()->notice("Finish! {$pref} {$time}sec ({$stat})");
        });

        return $e;
    }

    public static function seriesArrayTaskEvents(string $eventName = null)
    {
        $e = self::core();

        $e->put('beforeSeriesLoop', function ($e) use ($eventName) {
            $pref = $eventName ?? 'Series loop';
            logger()->notice("{$pref} (length: {$e->seriesLength})");
        });

        $e->put('beforeOuterLoop', function ($e) {
            $prog = self::progresString($e->seriesIndex, $e->seriesLength, true, '{', '}');
            $task = self::wrapString($e->outerProps, 60);
            $total = self::wrapCount($e->execProps);
            logger()->info("{$prog} Task {$task} (length: {$e->outerLength}, total: {$total})");
        });

        /// ////////////////////////////////////////

        $e->put('outerLooped', function ($e) {
            $prog = self::progresString($e->seriesIndex, $e->seriesLength, true, '{', '}');
            $task = self::wrapString($e->outerProps, 60);
            $stat = self::rateStatString($e, 'outer');
            logger()->info("{$prog} Task finish {$task} ({$stat})");
        });

        $e->put('seriesLooped', function ($e) use ($eventName) {
            $pref = $eventName ?? 'Series loop';
            $time = number_format(microtime(true) - $e->startTimestamp, 2);
            $stat = self::rateStatString($e, 'series');
            logger()->notice("Finish! {$pref} {$time}sec ({$stat})");
        });

        return $e;
    }

    /// ////////////////////////////////////////////////////////////
    /// ////////////////////////////////////////////////////////////
    /// ////////////////////////////////////////////////////////////

    private static function core(bool $showDenominator = false)
    {
        $e = collect();

        $e->put('beforeInnerLoop', function ($e) use ($showDenominator) {
            $prog = self::progresString($e->outerIndex, $e->outerLength, $showDenominator, '<', '>');
            logger()->info("{$prog} Handle loop (length: {$e->innerLength})");
        });

        $e->put('fetched', function ($e) use ($showDenominator) {
            $prog = self::progresString($e->outerIndex, $e->outerLength, $showDenominator, '<', '>');
            $count = self::wrapCount($e->fetchResponse);
            logger()->info("{$prog} Fetch {$count} items");
        });

        /// ////////////////////////////////////////

        $e->put('innerSuccess', function ($e) {
            $prog = self::progresString($e->innerIndex, $e->innerLength, true, '[', ']');
            $method = $e->handleMethod ?? 'success';
            $res = self::wrapString($e->handleResponse, 60);
            logger()->debug("{$prog} {$method}: {$e->innerKey} => {$res}");
        });
        $e->put('innerSkip', function ($e) {
            $prog = self::progresString($e->innerIndex, $e->innerLength, true, '[', ']');
            $method = 'skip';
            logger()->debug("{$prog} {$method}: {$e->innerKey}");
        });
        $e->put('innerException', function ($e) {
            $prog = self::progresString($e->innerIndex, $e->innerLength, true, '[', ']');
            $method = 'throw';
            logger()->error("{$prog} {$method}: {$e->innerKey}");
            logger()->error($e->exception); // 例外も吐いとく
        });

        /// ////////////////////////////////////////

        $e->put('innerLooped', function ($e) use ($showDenominator) {
            $prog = self::progresString($e->outerIndex, $e->outerLength, $showDenominator, '<', '>');
            $stat = self::rateStatString($e, 'outer');
            logger()->info("{$prog} Handle loop finish ({$stat})");
        });

        return $e;
    }

    private static function wrapString($res, $limit = 0)
    {
        $str = $res;
        if (is_iterable($res)) {
            $str = json_encode($res, JSON_UNESCAPED_UNICODE);
        }

        // 文字数制限
        if ($limit > 0) {
            $len = strlen($str);
            if ($len > $limit - 3) {
                $str = substr($str, 0, $limit).'...';
            }
        }
        return $str;
    }

    private static function wrapCount($res)
    {
        if (is_countable($res)) {
            return count($res);
        }
        return $res ? 1 : 0;
    }

    private static function rateStatString($e, $prefix)
    {
        // 項目が多すぎるから prefix から導出（融通が効かない弱点）
        $total = $e->get($prefix.'Total');
        $success = $e->get($prefix.'Success');
        $skip = $e->get($prefix.'Skip');
        $throw = $e->get($prefix.'Throw');

        $rate = $total !== 0
            ? number_format((($success + $skip) / $total) * 100, 2)
            : number_format(100, 2);
        $stat = "{$rate}%, {$success}+{$skip}/{$total}, skip: {$skip}, throw: {$throw}";
        return $stat;
    }

    private static function progresString($numer, $denom, $showDenominator = true, $bracket = '(', $endBracket = ')')
    {
        $val = $showDenominator ? "{$numer}/{$denom}" : $numer;
        return $bracket.$val.$endBracket;
    }
}
