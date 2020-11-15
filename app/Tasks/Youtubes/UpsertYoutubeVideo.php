<?php

namespace App\Tasks\Youtubes;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\ExecTaskBuilder;
use App\Lib\TaskBuilder\Events\TaskEventer;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeVideoParser;
use App\Models\Video;

class UpsertYoutubeVideo extends ExecTaskBuilder
{
    protected $createNewChannel = false; // true で channel が無いとき作成する(優先度高)
    protected $skipNewChannel = false; // true で channel が無いときスキップする
    protected $skipExistVideo = false; // true で 存在してる video を除外する

    public function createNewChannel(bool $val = true)
    {
        $this->createNewChannel = $val;
        return $this;
    }

    public function skipNewChannel(bool $val = true)
    {
        $this->skipNewChannel = $val;
        return $this;
    }

    public function skipExistVideo(bool $val = true)
    {
        $this->skipExistVideo = $val;
        return $this;
    }

    ///

    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        return $builder
            ->process('filter', $this->filter(), true)
            ->process('chunk', $this->chunk(10), true)
            ->loop('chunk', function (TaskBuilder $builder) {
                $builder
                    ->mappingProcess('fetch', $this->fetch(), $this->keyOfItem())
                    ->loop('handle', function (TaskBuilder $builder) {
                        $builder->process('insert', $this->insert());
                    });
            })
            ->process('flatten', $this->flatten());
    }

    ///

    private function filter()
    {
        return function ($val) {
            if ($this->skipExistVideo) {
                // DBに存在しないのを抽出する
                $val = $val->filter(function ($e) {
                    return !(Video::where(['code' => $e])->exists());
                });
            }
            return $val;
        };
    }

    private function fetch()
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        return function ($val) {
            $ids = $val->toArray();
            $parts = ['id, snippet, contentDetails, statistics, status, liveStreamingDetails'];
            $items = YoutubeAPI::getVideoInfo($ids, $parts);
            return $items;
        };
    }

    private function keyOfItem()
    {
        return function ($val) {
            return data_get($val, 'id');
        };
    }

    private function insert()
    {
        return function ($val, TaskEventer $e, $key = null) {
            $parse = null;

            if (!$val) {
                // 値が無い = YouTube から削除されている -> 削除処理へ
                $parse = YoutubeVideoParser::delete($key);
            } else {
                // 通常処理
                $parse = YoutubeVideoParser::insert($val, $this->createNewChannel, $this->skipNewChannel);
            }

            return $parse;
        };
    }
}
