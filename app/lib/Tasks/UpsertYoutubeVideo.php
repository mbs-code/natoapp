<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\ChunkUpsertTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeVideoParser;

class UpsertYoutubeVideo extends ChunkUpsertTask
{
    protected $itemLengthOnce = 50;

    protected function fetch($items)
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $parts = ['id, snippet, contentDetails, statistics, status, liveStreamingDetails'];
        $items = YoutubeAPI::getVideoInfo($items, $parts);
        return $items;
    }

    protected function parse($item)
    {
        $parse = YoutubeVideoParser::insert($item);
        return $parse;
    }
}
