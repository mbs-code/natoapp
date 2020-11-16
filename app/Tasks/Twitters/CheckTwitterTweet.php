<?php

namespace App\Tasks\Twitters;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\ExecTaskBuilder;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use Illuminate\Support\Str;

class CheckTwitterTweet extends ExecTaskBuilder
{
    protected $nextFlag = false; // 次へ進むかどうかのフラグ
    protected $since_id = null; // 取得する最古のID, 含まれない
    protected $latestId = null; // 最新のID, 最初に入れとく
    protected $oldestId = null; // 最後に取得したID, 毎ループ更新する

    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        return $builder
            ->whileLoop('chunk', function (TaskBuilder $builder) {
                $builder
                    ->process('fetch', $this->fetch())
                    ->loop('handle', function (TaskBuilder $builder) {
                        $builder->process('parse', $this->parse());
                    });
            }, $this->canNext())
            ->process('flatten', $this->flatten());
    }

    ///

    private function canNext()
    {
        return function () {
            if ($this->nextFlag) {
                $this->nextFlag = false;
                return true;
            }
            return false;
        };
    }

    ///

    private function fetch()
    {
        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api/v1/tweets/timelines/api-reference/get-statuses-user_timeline
        // 1500 call/15min, 100,000 call/1day
        return function ($val) {
            // since_id < id <= max_id: 最新順で取得
            $maxId = 5;
            $params = [
                'screen_name' => $val,
                'count' => $maxId, // max: 200?
                'format' => 'object',
                'since_id' => '1327848954269106178', // 含めない
                'trim_user' => true, // user 情報を削除
            ];

            if ($this->oldestId) {
                $params['max_id'] = $this->oldestId - 1; // 自身を含めちゃうので -1 する
            }

            $res = TwitterAPI::getUserTimeline($params);
            $items = collect($res);

            // 後処理
            // latest が空なら追加しとく
            if ($this->latestId) {
                $this->latestId = data_get($items->first(), 'id_str');
            }

            // 一つも取得できてない or 最大数取れてないなら終了
            // TODO: 取れないときもあるかも？
            if ($items->count() === 0 || $items->count() < $maxId) {
                $this->nextFlag = true;
            }

            // 最後に取得したIDを保存
            $this->oldestId = data_get($items->last(), 'id_str');

            return $items;
        };
    }

    private function parse()
    {
        return function($val) {
            $text = data_get($val, 'text');
            $text = Str::of($text)
                ->replaceMatches('/\n|\r|\r\n/', '<br>')
                ->limit(80)
                ->__toString();
            return $text;

            // TODO: youtube の URLが取れんぞ！

            // // とりあえずリンクを全部抜き出す
            // $urls = collect()
            //     ->concat(data_get($val, 'entities.urls', []))
            //     ->concat(data_get($val, 'entities.media', []))
            //     ->concat(data_get($val, 'quoted_status.entities.urls', []))
            //     ->concat(data_get($val, 'quoted_status.entities.media', []))
            //     ->map(function ($e) {
            //         return data_get($e, 'expanded_url');
            //     });

            // // url を判定していく
            // $counter = 0;
            // $links = [
            //     'youtubes' => [],
            // ];

            // foreach($urls as $url) {
            //     // youtube
            //     if (strpos($url, '//youtu.be') !== false) {
            //         $innerParse = parse_url($url);
            //         $vid = Str::of(data_get($innerParse, 'path', ''))
            //             ->explode('/')->last();
            //             logger()->info($vid);
            //         if ($vid) {
            //             $links['youtubes'][] = $vid;
            //             $counter ++;
            //         }
            //     }
            // }

            // // リンクを返却, 1つもリンクが無ければ skip
            // return $counter > 0 ? $links : false;
        };
    }
}
