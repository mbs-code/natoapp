<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\ChunkUpsertTask;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use App\Lib\Parsers\TwitterUserParser;

class UpsertTwitterUser extends ChunkUpsertTask
{
    protected $itemLengthOnce = 100;

    protected function fetch($items)
    {
        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api
        $params = [
            'screen_name' => implode(',', $items), // max: 100
            'format' => 'object'
        ];
        $items = TwitterAPI::getUsersLookup($params);
        return $items;
    }

    protected function parse($item)
    {
        $parse = TwitterUserParser::insert($item);
        return $parse;
    }
}
