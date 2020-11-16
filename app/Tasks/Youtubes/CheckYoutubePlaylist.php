<?php

namespace App\Tasks\Youtubes;

use App\Lib\TaskBuilder\TaskBuilder;
use App\Lib\TaskBuilder\ExecTaskBuilder;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Models\Youtube;

class CheckYoutubePlaylist extends ExecTaskBuilder
{
    protected $nextPageToken = null; // 次のページトークン
    protected $loopCount = 0; // ループ回数

    protected $maxLoop = 1; // 最大ループ数, 0 以下で無限

    public function maxLoop(int $val)
    {
        $this->maxLoop = $val;
        return $this;
    }

    ///

    protected function generateTaskflow(TaskBuilder $builder): TaskBuilder
    {
        return $builder
            ->whileLoop('chunk', function (TaskBuilder $builder) {
                $builder
                    ->process('trans', $this->trans())
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
            // ページトークンが無くなったら終了
            if ($this->nextPageToken === false) {
                $this->nextPageToken = null;
                $this->loopCount = 0;
                return true;
            }

            // 最大ループに達したら終了
            $max = $this->maxLoop;
            if ($max > 0 && $this->loopCount >= $max) {
                $this->nextPageToken = null;
                $this->loopCount = 0;
                return true;
            }

            return false;
        };
    }

    ///

    private function trans()
    {
        return function ($val) {
            $c = Youtube::select('playlist')->where('code', $val)->first();
            $pid = data_get($c, 'playlist');
            return $pid ?? null;
        };
    }

    private function fetch()
    {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        return function ($val) {
            $this->loopCount ++;

            $res = YoutubeAPI::getPlaylistItemsByPlaylistId($val, $this->nextPageToken, 50, ['id', 'snippet']);
            $this->nextPageToken = data_get($res, 'info.nextPageToken');

            $items = collect(data_get($res, 'results'), []);
            return $items;
        };
    }

    private function parse()
    {
        return function ($val) {
            $id = data_get($val, 'snippet.resourceId.videoId');
            return $id ?? false;
        };
    }
}
