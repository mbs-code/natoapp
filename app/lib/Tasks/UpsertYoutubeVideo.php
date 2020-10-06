<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\ChunkUpsertTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeVideoParser;
use Illuminate\Database\Eloquent\Model;

class UpsertYoutubeVideo extends ChunkUpsertTask
{
    protected $itemLengthOnce = 50;

    protected $notExistChannel = false; // true で channel も保存する

    public function notExistChannel(bool $val)
    {
        $this->notExistChannel = $val;
        return $this;
    }

    protected function fetch($items): array
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $parts = ['id, snippet, contentDetails, statistics, status, liveStreamingDetails'];
        $items = YoutubeAPI::getVideoInfo($items, $parts);
        return $items;
    }

    protected function parse($item): Model
    {
        $parse = YoutubeVideoParser::insert($item, $this->notExistChannel);
        return $parse;
    }
}
