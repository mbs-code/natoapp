<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\ArrayChunkUpsertTask;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use App\Lib\Parsers\TwitterUserParser;

class UpsertTwitterUser extends ArrayChunkUpsertTask
{
    protected $chunkSize = 100;

    protected function fetch($var)
    {
        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api
        $names = is_array($var) ? implode(',', $var) : $var;
        $params = [
            'screen_name' => $names, // max: 100
            'format' => 'object'
        ];
        $items = TwitterAPI::getUsersLookup($params);
        return $items;
    }

    protected function handle($item)
    {
        $parse = TwitterUserParser::insert($item);
        return $parse;
    }
}
