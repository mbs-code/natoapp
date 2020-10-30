<?php

namespace App\Lib\Tasks\Utils;


class GeneralEvents
{
    public static function arrayTaskEvents(string $eventName = null) {
        $e = self::core(true);

        $e->put('beforeOuterLoop', function ($e) use ($eventName) {
            $total = self::wrapCount($e->execProps);
            $prefStr = $eventName ?? 'Task';
            $mes = "{$prefStr} (length: {$e->outerLength}, total: {$total})";
            logger()->notice($mes);
        });

        /// ////////////////////////////////////////

        $e->put('outerLooped', function ($e) use ($eventName) {
            $pref = $eventName ? $eventName : 'Outer loop';
            $time = number_format(microtime(true) - $e->startTimestamp, 2);
            $rate = $e->outerTotal !== 0
                ? number_format((($e->outerSuccess + $e->outerSkip) / $e->outerTotal) * 100, 2)
                : number_format(100, 2);
            $stat = "{$rate}%, {$e->outerSuccess}+{$e->outerSkip}/{$e->outerTotal}, skip: {$e->outerSkip}, throw: {$e->outerThrow}";
            $mes = "Finish! {$pref} {$time}sec ({$stat})";
            logger()->notice($mes);
        });

        return $e;
    }

    public static function seriesArrayTaskEvents(string $eventName = null) {
        $e = self::core();

        $e->put('beforeSeriesLoop', function ($e) use ($eventName) {
            $prefStr = $eventName ?? 'Series loop';
            $mes = "{$prefStr} (length: {$e->seriesLength})";
            logger()->notice($mes);
        });

        $e->put('beforeOuterLoop', function ($e) {
            $total = self::wrapCount($e->execProps);
            $task = $e->outerProps;
            $mes = "<{$e->seriesIndex}/{$e->seriesLength}> Task {$task} (length: {$e->outerLength}, total: {$total})";
            logger()->info($mes);
        });

        /// ////////////////////////////////////////

        $e->put('outerLooped', function ($e) {
            $task = $e->outerProps;
            $rate = $e->outerTotal !== 0
                ? number_format((($e->outerSuccess + $e->outerSkip) / $e->outerTotal) * 100, 2)
                : number_format(100, 2);
            $stat = "{$rate}%, {$e->outerSuccess}+{$e->outerSkip}/{$e->outerTotal}, skip: {$e->outerSkip}, throw: {$e->outerThrow}";
            $mes = "<{$e->seriesIndex}/{$e->seriesLength}> Task finish {$task} ({$stat})";
            logger()->info($mes);
        });

        $e->put('seriesLooped', function ($e) use ($eventName) {
            $pref = $eventName ? $eventName : 'Series loop';
            $time = number_format(microtime(true) - $e->startTimestamp, 2);
            $rate = $e->seriesTotal !== 0
                ? number_format((($e->seriesSuccess + $e->seriesSkip) / $e->seriesTotal) * 100, 2)
                : number_format(100, 2);
            $stat = "{$rate}%, {$e->seriesSuccess}+{$e->seriesSkip}/{$e->seriesTotal}, skip: {$e->seriesSkip}, throw: {$e->seriesThrow}";
            $mes = "Finish! {$pref} {$time}sec ({$stat})";
            logger()->notice($mes);
        });

        return $e;
    }

    /// ////////////////////////////////////////////////////////////

    private static function wrapResponse($res)
    {
        if (is_array($res)) {
            return json_encode($res, JSON_UNESCAPED_UNICODE);
        }
        return $res;
    }

    private static function wrapCount($res)
    {
        if (is_countable($res)) {
            return count($res);
        }
        return $res ? 1 : 0;
    }

    private static function progresString($numer, $denom, $showDenominator = true)
    {
        return $showDenominator ? "<{$numer}/{$denom}>" : "{{$numer}}";
    }

    private static function core(bool $showDenominator = false)
    {
        $e = collect();

        $e->put('beforeInnerLoop', function ($e) use ($showDenominator) {
            $progStr = self::progresString($e->outerIndex, $e->outerLength, $showDenominator);
            $mes = "{$progStr} Handle loop (length: {$e->innerLength})";
            logger()->info($mes);
        });

        /// ////////////////////////////////////////

        $e->put('fetched', function ($e) use ($showDenominator) {
            $progStr = self::progresString($e->outerIndex, $e->outerLength, $showDenominator);
            $count = self::wrapCount($e->fetchResponse);
            $mes = "{$progStr} Fetch {$count} items";
            logger()->info($mes);
        });

        $e->put('innerSuccess', function ($e) use ($showDenominator) {
            $progStr = self::progresString($e->innerIndex, $e->innerLength, $showDenominator);
            $methodStr = $e->handleMethod ?? 'success';
            $res = self::wrapResponse($e->handleResponse);
            $mes = "{$progStr} {$methodStr}: {$e->innerKey} => {$res}";
            logger()->debug($mes);
        });
        $e->put('innerSkip', function ($e) use ($showDenominator) {
            $progStr = self::progresString($e->innerIndex, $e->innerLength, $showDenominator);
            $methodStr = 'skip';
            $mes = "{$progStr}  {$methodStr}: {$e->innerKey}";
            logger()->debug($mes);
        });
        $e->put('innerException', function ($e) use ($showDenominator) {
            $progStr = self::progresString($e->innerIndex, $e->innerLength, $showDenominator);
            $methodStr = 'throw';
            $mes = "{$progStr} {$methodStr}: {$e->innerKey}";
            logger()->error($mes);
            logger()->error($e->exception); // 例外も吐いとく
        });

        /// ////////////////////////////////////////

        $e->put('innerLooped', function ($e) use ($showDenominator) {
            $progStr = self::progresString($e->outerIndex, $e->outerLength, $showDenominator);
            $rate = $e->innerLength !== 0
                ? number_format((($e->innerSuccess + $e->innerSkip) / $e->innerLength) * 100, 2)
                : number_format(100, 2);
            $stat = "{$rate}%, {$e->innerSuccess}+{$e->innerSkip}/{$e->innerLength}, skip: {$e->innerSkip}, throw: {$e->innerThrow}";
            $mes = "{$progStr} Handle loop finish ({$stat})";
            logger()->info($mes);
        });

        return $e;
    }
}
