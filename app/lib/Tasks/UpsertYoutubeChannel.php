<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\ChunkUpsertTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeChannelParser;

class UpsertYoutubeChannel extends ChunkUpsertTask
{
    protected $itemLengthOnce = 50;

    protected function fetch($items)
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $params = [
            'part' => 'id, snippet, statistics, contentDetails, brandingSettings',
            'maxResults' => 50 // max: 50
        ];
        $items = YoutubeAPI::getChannelById($items, $params);
        return $items;
    }

    protected function parse($item)
    {
        $parse = YoutubeChannelParser::insert($item);
        return $parse;
    }
}
