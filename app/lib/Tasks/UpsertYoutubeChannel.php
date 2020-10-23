<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\ChunkFetchArrayTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeChannelParser;
use App\Exceptions\NullPointerException;

class UpsertYoutubeChannel extends ChunkFetchArrayTask
{
    protected $doMapping = true;
    protected $chunkSize = 50;

    protected function fetch($var)
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $params = [
            'part' => 'id, snippet, statistics, contentDetails, brandingSettings',
            'maxResults' => 50 // max: 50
        ];
        $items = YoutubeAPI::getChannelById($var, $params);

        return $items;
    }

    protected function getKeyCallback($item, $keys) {
        return data_get($item, 'id');
    }

    protected function handle($item)
    {
        if (!$item) {
            // TODO: 値が無い = YouTube から削除されている。今のとこ手動対応
            throw new NullPointerException();
        }

        $parse = YoutubeChannelParser::insert($item);
        return $parse;
    }
}
