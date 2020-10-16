<?php

namespace App\Lib\Tasks\Utils;


class GeneralEvents
{
    public static function arrayTaskEvents(string $eventName = null) {
        $e = collect();

        $e->put('beforeOuterLoop', function ($e) use ($eventName) {
            $total = count($e->execProps);
            $pref = $eventName ? $eventName : 'Outer loop';
            $mes = "{$pref} (length: {$e->outerLength}, total: {$total})";
            logger()->notice($mes);
        });
        $e->put('beforeInnerLoop', function ($e) {
            $mes = "<{$e->outerIndex}/{$e->outerLength}> Loop (length: {$e->innerLength})";
            logger()->info($mes);
        });

        /// ////////////////////////////////////////

        $e->put('fetched', function ($e) {
            $count = count($e->fetchResponse);
            $mes = "<{$e->outerIndex}/{$e->outerLength}> Fetch {$count} items";
            logger()->info($mes);
        });

        $e->put('innerSuccess', function ($e) {
            $method = $e->handleMethod ?? 'success';
            $mes = "[{$e->innerIndex}/{$e->innerLength}] {$method}: {$e->innerKey} => {$e->handleResponse}";
            logger()->debug($mes);
        });
        $e->put('innerSkip', function ($e) {
            $mes = "[{$e->innerIndex}/{$e->innerLength}] skip: {$e->innerKey}";
            logger()->debug($mes);
        });
        $e->put('innerException', function ($e) {
            $mes = "[{$e->innerIndex}/{$e->innerLength}] throw: {$e->innerKey}";
            logger()->error($mes);
            logger()->error($e->exception);
        });

        /// ////////////////////////////////////////

        $e->put('innerLooped', function ($e) {
            $rate = number_format((($e->innerSuccess + $e->innerSkip) / $e->innerLength) * 100, 2);
            $stat = "{$rate}%, {$e->innerSuccess}+{$e->innerSkip}/{$e->innerLength}, skip: {$e->innerSkip}, throw: {$e->innerThrow}";
            $mes = "<{$e->outerIndex}/{$e->outerLength}> Loop finish ({$stat})";
            logger()->info($mes);
        });
        $e->put('outerLooped', function ($e) use ($eventName) {
            $pref = $eventName ? $eventName : 'Outer loop';
            $time = number_format(microtime(true) - $e->startTimestamp, 2);
            $rate = number_format((($e->outerSuccess + $e->outerSkip) / $e->outerTotal) * 100, 2);
            $stat = "{$rate}%, {$e->outerSuccess}+{$e->outerSkip}/{$e->outerTotal}, skip: {$e->outerSkip}, throw: {$e->outerThrow}";
            $mes = "Finish! {$pref} {$time}sec ({$stat})";
            logger()->notice($mes);
        });

        return $e;
    }
}
