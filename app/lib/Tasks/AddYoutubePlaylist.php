<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\UpsertTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Models\Youtube;

class AddYoutubePlaylist extends UpsertTask
{
    // protected $notExistChannel = false; // true で channel も保存する

    // public function notExistChannel(bool $val)
    // {
    //     $this->notExistChannel = $val;
    //     return $this;
    // }

    protected $nextPageToken = null;

    // @override
    protected function preFormat($var)
    {
        $channel = Youtube::where([ 'code' => $var ])->first();
        $playlistId = data_get($channel, 'playlist');
        if (!$channel ?? !$playlistId) {
            echo('error'.PHP_EOL);
            throw new Error('error!');
        }

        return $playlistId;
    }

    protected function fetch($var)
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $res = YoutubeAPI::getPlaylistItemsByPlaylistId($var, $this->nextPageToken, 50, ['id', 'snippet']);
        $this->nextPageToken = data_get($res, 'info.nextPageToken');

        $items = data_get($res, 'results');
        $ids = collect($items ?? [])->pluck('snippet.resourceId.videoId')->toArray();
        return $ids;
    }

    protected function handle($item)
    {
        $res = UpsertYoutubeVideo::runs($item);
        return $res;
    }
}
