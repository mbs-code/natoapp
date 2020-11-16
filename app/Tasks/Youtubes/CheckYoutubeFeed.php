<?php

namespace App\Tasks\Youtubes;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\ExecTaskBuilder;
use GuzzleHttp\Client;

class CheckYoutubeFeed extends ExecTaskBuilder
{
    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        return $builder
            ->loop('chunk', function (TaskBuilder $builder) {
                $builder
                    ->process('fetch', $this->fetch())
                    ->loop('handle', function (TaskBuilder $builder) {
                        $builder->process('parse', $this->parse());
                    });
            })
            ->process('flatten', $this->flatten());
    }

    ///

    private function fetch()
    {
        return function ($val) {
            $url = "https://www.youtube.com/feeds/videos.xml?channel_id={$val}";

            $client = new Client();
            $res = $client->request('GET', $url);
            $contents = $res->getBody()->getContents();
            $xml = simplexml_load_string($contents);

            $ary = json_decode(json_encode($xml), true);
            $items = collect(data_get($ary, 'entry'));

            return $items;
        };
    }

    private function parse()
    {
        return function ($val) {
            $id = str_replace('yt:video:', '', data_get($val, 'id', ''));
            return $id ?? false;
        };
    }
}
