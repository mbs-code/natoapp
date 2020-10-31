<?php

namespace App\Lib\Tasks;

use Nesk\Puphpeteer\Puppeteer;
use App\Lib\Tasks\Bases\FetchArrayTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\lib\TimeUtil;
use App\Models\Profile;

class AddProfileFromYoutubeChannel extends FetchArrayTask
{
    protected function fetch($channelID)
    {
        $profilables = [
            'twitters' => [],
            'youtubes' => [$channelID],
        ];

        $url = 'https://www.youtube.com/channel/'.$channelID;

        $puppeteer = new Puppeteer();
        $browser = $puppeteer->launch([
            // 'args' => ['--no-sandbox', '--disable-setuid-sandbox']
        ]);

        $page = $browser->newPage();
        $page->goto($url);

        // youtube channel 右上のリンク集を取得 (primary と secondary(内部配列) がある)
        $as = $page->querySelectorAll('#links-holder .yt-simple-endpoint');
        foreach ($as as $a) {
            $jsHandle = $a->getProperty('href');
            if ($jsHandle) {
                $href = $jsHandle->jsonValue();
                $parse = parse_url($href);

                $query = [];
                parse_str(data_get($parse, 'query'), $query);
                $q = data_get($query, 'q');

                // url の type 判定処理
                if ($q) {
                    // twitter
                    if (strpos($q, '//twitter.com') !== false) {
                        $innerParse = parse_url($q);
                        $screenName = Str::of(data_get($innerParse, 'path', ''))
                            ->explode('/')->last();
                        if ($screenName) {
                            $profilables['twitters'][] = $screenName;
                        }
                    }
                }
            }
        }

        $browser->close();

        return [$profilables];
    }

    protected function getKeyCallback($item, $keys) {
        return collect($keys)->first();
    }

    protected function handle($data) {
        $profile = DB::transaction(function () use ($data)
        {
            // twitter 生成
            $twitters = collect(data_get($data, 'twitters', []))
                ->map(function($item) {
                    return UpsertTwitterUser::run($item)->first();
                });

            // youtube 生成
            $youtubes = collect(data_get($data, 'youtubes', []))
                ->map(function($item) {
                    return UpsertYoutubeChannel::run($item)->first();
                });

            // name 推測
            $name1 = data_get($twitters->first(), 'name');
            $name2 = data_get($youtubes->first(), 'name');
            logger()->debug("twitter name: {$name1}");
            logger()->debug("youtube name: {$name2}");

            $diff = Helper::chooseStringDiff($name1, $name2);
            $name = strlen($diff) >= 3 ? $diff : ($name1 ?? $name2);

            // profile 生成
            $dt = TimeUtil::LocaleCarbonNow()->format('Y-m-d H:i:s');
            $profile = new Profile();
            $profile->fill([
                'name' => $name,
                'description' => $dt.' Auto Created.',
            ]);
            $profile->save();

            $profile->twitters()->sync($twitters->pluck('id'));
            $profile->youtubes()->sync($youtubes->pluck('id'));

            return $profile;
        });

        return $profile;
    }
}
