<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\ChunkFetchArrayTask;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeVideoParser;
use App\Models\Video;
use App\Enums\VideoStatus;

class UpsertYoutubeVideo extends ChunkFetchArrayTask
{
    protected $doMapping = true;
    protected $chunkSize = 50;

    protected $createNewChannel = false; // true で channel が無いとき作成する(優先度高)
    protected $skipNewChannel = false; // true で channel が無いときスキップする
    protected $skipExistVideo = false; // true で 存在してる video を除外する

    public function createNewChannel(bool $val)
    {
        $this->createNewChannel = $val;
        return $this;
    }

    public function skipNewChannel(bool $val)
    {
        $this->skipNewChannel = $val;
        return $this;
    }

    public function skipExistVideo(bool $val)
    {
        $this->skipExistVideo = $val;
        return $this;
    }

    /// ////////////////////////////////////////////////////////////

    protected function preFormat($var)
    {
        // DBに存在しないのを抽出する
        if ($this->skipExistVideo) {
            $var = $var->filter(function ($e) {
                return !Video::where(['code' => $e])->exists();
            });
        }
        return parent::preFormat($var);
    }

    protected function fetch($var)
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $ids = $var->toArray();
        $parts = ['id, snippet, contentDetails, statistics, status, liveStreamingDetails'];
        $items = YoutubeAPI::getVideoInfo($ids, $parts);
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
            $parse = YoutubeVideoParser::insert($item, $this->createNewChannel, $this->skipNewChannel);
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
