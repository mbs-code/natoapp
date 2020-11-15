<?php

namespace App\Tasks\Twitters;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\ExecTaskBuilder;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use App\Lib\Parsers\TwitterUserParser;
use App\Exceptions\NullPointerException;

class UpsertTwitterUser extends ExecTaskBuilder
{
    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        return $builder
            ->process('chunk', $this->chunk(100), true)
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
        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api
        return function($val) {
            $names = $val->implode(',');
            $params = [
                'screen_name' => $names, // max: 100
                'format' => 'object'
            ];

            $items = TwitterAPI::getUsersLookup($params);
            return $items;
        };
    }

    private function keyOfItem()
    {
        return function($val) {
            return data_get($val, 'screen_name');
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
            $parse = TwitterUserParser::insert($val);
            return $parse;
        };
    }
}
