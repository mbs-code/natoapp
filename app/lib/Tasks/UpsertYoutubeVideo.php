<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\ChunkFetchArrayTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeVideoParser;
use App\Models\Video;
use App\Enums\VideoStatus;

class UpsertYoutubeVideo extends ChunkFetchArrayTask
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

    protected function getKeyCallback($item, $keys) {
        return data_get($item, 'id');
    }

    protected function handle($item)
    {
        $parse = null;

        if (!$item) {
            // 値が無い = YouTube から削除されている -> 削除処理へ
            $key = $this->getEventAttr('innerKey');
            $parse = YoutubeVideoParser::delete($key);
        } else {
            $parse = YoutubeVideoParser::insert($item, $this->notExistChannel);
        }

        // 処理結果を event から読み出せるように
        if ($parse instanceof Video) {
            if (VideoStatus::DELETE()->equals($parse->status)) {
                // TODO: delete に切り替えた時だけでもいいかも
                $this->setEventAttr('handleMethod', 'delete');
            } else {
                if ($parse->wasRecentlyCreated) {
                    $this->setEventAttr('handleMethod', 'create');
                } else {
                    $this->setEventAttr('handleMethod', 'update');
                }
            }
        }

        return $parse;
    }
}
