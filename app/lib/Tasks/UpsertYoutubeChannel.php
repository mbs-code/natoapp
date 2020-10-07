<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\ArrayChunkUpsertTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeChannelParser;

class UpsertYoutubeChannel extends ArrayChunkUpsertTask
{
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

    protected function handle($item)
    {
        $parse = YoutubeChannelParser::insert($item);
        return $parse;
    }
}
