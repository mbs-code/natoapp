<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\ChunkUpsertTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeVideoParser;

class UpsertYoutubeVideo extends ChunkUpsertTask
{
    protected $chunkSize = 50;

    protected $notExistChannel = false; // true で channel も保存する

    public function notExistChannel(bool $val)
    {
        $this->notExistChannel = $val;
        return $this;
    }

    protected function fetch($var)
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $parts = ['id, snippet, contentDetails, statistics, status, liveStreamingDetails'];
        $items = YoutubeAPI::getVideoInfo($var, $parts);
        return $items;
    }

    protected function process($item)
    {
        $parse = YoutubeVideoParser::insert($item, $this->notExistChannel);
        return $parse;
    }
}
