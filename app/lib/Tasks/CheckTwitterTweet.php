<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\SeriesFetchArrayTask;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use Illuminate\Support\Str;
use Exception;
use Iterator;

class CheckTwitterTweet extends SeriesFetchArrayTask
{
    private $nextFlag = false;
    private $newestId = null;
    private $oldestId = null; // 最後に取得したID, 毎ループ更新する

    private $maxCount = 200; // max 200?

    // @override
    protected function preFormat($var)
    {
        // 値の初期化
        $this->nextFlag = false;
        $this->oldestId = null;

        logger()->notice('初期化');
        return $var;
    }

    // @override
    protected function outerLoopNext(Iterator $it)
    {
        if ($this->nextFlag) {
            $this->nextFlag = false;
            $it->next();
        }
    }

    protected function fetch($var)
    {
        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api/v1/tweets/timelines/api-reference/get-statuses-user_timeline
        // 1500 call/15min, 100000 call/1day
        $name = collect($var)->first();
        $params = [
            'screen_name' => $name,
            'count' => $this->maxCount, // max: 200?
            'format' => 'object',
            'since_id' => '1319712532727091200' // 含めない
        ];

        if ($this->oldestId) {
            $params['max_id'] = $this->oldestId - 1; // 自身を含めるので -1 する
        }

        $items = TwitterAPI::getUserTimeline($params);
        $col = collect($items);

        // 一つも取得できてない or 最大数取れてないなら終了
        if ($col->count() === 0 || $col->count() < $this->maxCount) {
            $this->nextFlag = true;
        }

        // 最後に取得したIDを保存
        $this->oldestId = data_get($col->last(), 'id_str');

        return $items;
    }

    protected function handle($item)
    {
        // TODO: sana_natori のURLが取れんぞ！

        // logger()->info('https://twitter.com/chieri_kakyoin/status/'.$item->id_str);
        logger()->info(json_encode($item, JSON_UNESCAPED_UNICODE));
        // とりあえずリンクを全部抜き出す
        $urls = collect()
            ->concat(data_get($item, 'entities.urls', []))
            ->concat(data_get($item, 'entities.media', []))
            ->concat(data_get($item, 'quoted_status.entities.urls', []))
            ->concat(data_get($item, 'quoted_status.entities.media', []))
            ->map(function ($e) {
                return data_get($e, 'expanded_url');
            });

        // url を判定していく
        $counter = 0;
        $links = [
            'youtubes' => [],
        ];

        foreach($urls as $url) {
            // youtube
            if (strpos($url, '//youtu.be') !== false) {
                $innerParse = parse_url($url);
                $vid = Str::of(data_get($innerParse, 'path', ''))
                    ->explode('/')->last();
                    logger()->info($vid);
                if ($vid) {
                    $links['youtubes'][] = $vid;
                    $counter ++;
                }
            }
        }

        return $counter > 0 ? $links : false;
    }
}
