<?php

namespace App\Lib\Tasks;

use Nesk\Puphpeteer\Puppeteer;
use App\Lib\Tasks\Bases\FetchArrayTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\lib\TimeUtil;
use App\Exceptions\DuplicateException;
use App\Models\Profile;

class AddProfileFromYoutubeChannel extends FetchArrayTask
{
    protected $duplicateRename = false; // 名前が重複したとき、連番を付与するか

    public function duplicateRename(bool $val)
    {
        $this->duplicateRename = $val;
        return $this;
    }

    /// ////////////////////////////////////////////////////////////

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
            logger()->debug("generate name: {$name}");

            // name が重複しているか確認
            while (true) {
                $doesntExist = Profile::where('name', $name)->doesntExist();
                if ($doesntExist) break;

                logger()->debug("-> is exist");
                if ($this->duplicateRename) {
                    // 末尾に _1` を付けるか数字を更新する
                    $names = Str::of($name)->explode('_');
                    $num = $names->pop(); // 末尾を取り出す
                    if ($names->count() > 0 && is_numeric($num)) {
                        $num = $num + 1;
                        $names->push($num);
                    } else {
                        $names->push($num);
                        $names->push('1');
                    }
                    $name = $names->implode('_');
                    logger()->debug("rename: {$name}");
                } else {
                    throw new DuplicateException("name = {$name}");
                }
            }

            ///

            // profile
            $dt = TimeUtil::LocaleCarbonNow()->format('Y-m-d H:i:s');
            $profile = new Profile();
            $profile->fill([
                'name' => $name,
                'description' => $dt.' Auto Created.',
            ]);
            $profile->save();

            // sync
            $profile->twitters()->sync($twitters->pluck('id'));
            $profile->youtubes()->sync($youtubes->pluck('id'));

            // cache
            $profile->cacheSync()->save();

            return $profile;
        });

        return $profile;
    }
}
