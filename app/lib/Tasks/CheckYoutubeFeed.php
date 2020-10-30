<?php

namespace App\Lib\Tasks;

use App\Lib\Tasks\Bases\SeriesFetchArrayTask;
use GuzzleHttp\Client;
use Iterator;

class CheckYoutubeFeed extends SeriesFetchArrayTask
{
    // @override
    protected function seriesLoopNext(Iterator $it)
    {
        parent::seriesLoopNext($it);
        // 次ループがあるなら一秒待機させる
        // TODO: task 処理に組み込んでも良いかも
        if ($it->valid()) {
            sleep(1);
        }
    }

    /// ////////////////////////////////////////////////////////////

    protected function fetch($var)
    {
        $cid = collect($var)->first();
        $url = "https://www.youtube.com/feeds/videos.xml?channel_id={$cid}";

        $client = new Client();
        $res = $client->request('GET', $url);
        $contents = $res->getBody()->getContents();
        $xml = simplexml_load_string($contents);

        $ary = json_decode(json_encode($xml), true);
        $entries = collect(data_get($ary, 'entry'));

        return $entries;
    }

    protected function handle($item)
    {
        $id = str_replace('yt:video:', '', data_get($item, 'id', ''));
        return $id ?? false;
    }
}
