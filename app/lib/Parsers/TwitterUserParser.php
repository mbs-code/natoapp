<?php

namespace App\Lib\Parsers;

use App\Lib\Parsers\Parser;
use App\Lib\TimeUtil;
use App\Models\Twitter;

class TwitterUserParser extends Parser
{
    public static function insert(object $item)
    {
        // twitter の生成
        $key  = data_get($item, 'screen_name');
        $tw = Twitter::firstOrNew(['screen_name' => $key]);

        $tw->code = data_get($item, 'id_str');
        $tw->name = data_get($item, 'name');
        $tw->screen_name = data_get($item, 'screen_name');
        $tw->location = data_get($item, 'location');
        $tw->description = data_get($item, 'description');
        $tw->url = data_get($item, 'url');
        $tw->thumbnail_url = str_replace('_normal.jpg', '.jpg', data_get($item, 'profile_image_url_https'));
        $tw->banner_url = str_replace('_normal.jpg', '.jpg', data_get($item, 'profile_banner_url'));

        $tw->protected = data_get($item, 'protected');
        $tw->published_at = TimeUtil::UTCToLocalCarbon(data_get($item, 'created_at'));

        $tw->followers = data_get($item, 'followers_count');
        $tw->friends = data_get($item, 'friends_count');
        $tw->listed = data_get($item, 'listed_count');
        $tw->favourites = data_get($item, 'favourites_count');
        $tw->statuses = data_get($item, 'statuses_count');

        $tw->save();
        return $tw;
    }
}
