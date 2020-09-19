<?php

namespace App\lib;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class Util
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
}
