<?php

namespace App\lib;

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class TimeUtil
{
    static function UTCToLocalCarbon($datetimeStr)
    {
        if ($datetimeStr) {
            $tz = Config::get('app.timezone') ?? 'UTC';
            $local = Carbon::parse($datetimeStr)->timezone($tz)->toDateTimeString();
            $datetime = Carbon::parse($local, 'UTC');
            return $datetime;
        }
        return $datetimeStr;
    }

    static function LocaleCarbonNow()
    {
        $tz = Config::get('app.timezone') ?? 'UTC';
        $local = Carbon::now()->timezone($tz)->toDateTimeString();
        $datetime = Carbon::parse($local, 'UTC');
        return $datetime;
    }

    static function parseDuration($durationStr)
    {
        if ($durationStr) {
            $duration = CarbonInterval::create($durationStr)->totalSeconds;
            return $duration;
        }
        return $durationStr;
    }
}
