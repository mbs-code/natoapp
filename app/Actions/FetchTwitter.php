<?php

namespace App\Actions;
use Thujohn\Twitter\Facades\Twitter as TwitterAPI;
use App\Models\Twitter;
use App\lib\Util;

class FetchTwitter {

    public static function handle(array $screenNames) {
        // doc: https://github.com/atymic/twitter
        // api ref: https://developer.twitter.com/en/docs/twitter-api
        $params = [
            'screen_name' => implode(',', $screenNames), // max: 100
            'format' => 'object'
        ];
        $items = TwitterAPI::getUsersLookup($params);

        $ret = collect();
        foreach ($items as $item) {
            $twitter = Twitter::firstOrNew(['screen_name' => $item->screen_name]);
            $twitter->code = $item->id;
            $twitter->name = $item->name;
            $twitter->screen_name = $item->screen_name;
            $twitter->location = $item->location;
            $twitter->description = $item->description;
            $twitter->url = $item->url;
            $twitter->thumbnail_url = str_replace('_normal.jpg', '.jpg', $item->profile_image_url_https);
            $twitter->banner_url = str_replace('_normal.jpg', '.jpg', $item->profile_banner_url);

            $twitter->protected = $item->protected;
            $twitter->published_at = Util::UTCToLocalCarbon($item->created_at);

            $twitter->followers = $item->followers_count;
            $twitter->friends = $item->friends_count;
            $twitter->listed = $item->listed_count;
            $twitter->favourites = $item->favourites_count;
            $twitter->statuses = $item->statuses_count;

            $twitter->save();
            $ret->push($twitter);
        }

        return $ret;
    }
}
