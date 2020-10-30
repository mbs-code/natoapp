<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\ChunkFetchArrayTask;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use App\Lib\Parsers\TwitterUserParser;
use App\Exceptions\NullPointerException;

class UpsertTwitterUser extends ChunkFetchArrayTask
{
    protected $doMapping = true;
    protected $chunkSize = 100;

    protected function fetch($var)
    {
        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api
        $names = $var->implode(',');
        $params = [
            'screen_name' => $names, // max: 100
            'format' => 'object'
        ];
        $items = TwitterAPI::getUsersLookup($params);
        return $items;
    }

    protected function getKeyCallback($item, $keys) {
        return data_get($item, 'screen_name');
    }

    protected function handle($item)
    {
        if (!$item) {
            // TODO: 値が無い = twitter から削除されている。今のとこ手動対応
            throw new NullPointerException();
        }

        $parse = TwitterUserParser::insert($item);
        return $parse;
    }
}
