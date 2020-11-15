<?php

namespace App\Tasks\Youtubes;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\ExecTaskBuilder;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Lib\Parsers\YoutubeChannelParser;
use App\Exceptions\NullPointerException;

class UpsertYoutubeChannel extends ExecTaskBuilder
{
    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        return $builder
            ->process('chunk', $this->chunk(50), true)
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

    private function fetch()
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        return function($val) {
            $ids = $val->toArray();
            $params = [
                'part' => 'id, snippet, statistics, contentDetails, brandingSettings',
                'maxResults' => 50, // max: 50
            ];
            $items = YoutubeAPI::getChannelById($ids, $params);

            return $items;
        };
    }

    private function keyOfItem()
    {
        return function($val) {
            return data_get($val, 'id');
        };
    }

    private function insert()
    {
        return function($val) {
            if (!$val) {
                // TODO: 値が無い = YouTube から削除されている。今のとこ手動対応
                throw new NullPointerException('No value');
            }

            // 変換して DB に挿入する
            $parse = YoutubeChannelParser::insert($val);
            return $parse;
        };
    }
}
