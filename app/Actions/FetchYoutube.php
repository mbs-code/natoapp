<?php

namespace App\Actions;
use Alaouy\Youtube\Facades\Youtube as YoutubeAPI;
use App\Models\Youtube;
use App\lib\Util;

class FetchYoutube {

    public static function handle(array $channelIds) {
        // doc: https://github.com/alaouy/Youtube
        // api ref: https://developers.google.com/youtube/v3/docs?hl=ja
        $params = [
            'part' => 'id, snippet, statistics, contentDetails, brandingSettings',
            'maxResults' => 50 // max: 50
        ];
        $items = YoutubeAPI::getChannelById($channelIds, $params);

        $ret = collect();
        foreach ($items as $item) {
            $key = data_get($item, 'id');

            $yt = Youtube::firstOrNew(['code' => $key]);
            $yt->code = data_get($item, 'id');
            $yt->name = data_get($item, 'snippet.title');
            $yt->description = data_get($item, 'snippet.description');
            $yt->playlist = data_get($item, 'contentDetails.relatedPlaylists.uploads');
            $yt->thumbnail_url = self::chooseYoutubeThumbnail(data_get($item, 'snippet.thumbnails'));
            $yt->banner_url = data_get($item, 'brandingSettings.image.bannerTvHighImageUrl');

            $yt->published_at = Util::UTCToLocalCarbon(data_get($item, 'snippet.publishedAt'));

            $yt->views = data_get($item, 'statistics.viewCount');
            $yt->comments = data_get($item, 'statistics.commentCount');
            $yt->subscribers = data_get($item, 'statistics.subscriberCount');
            $yt->videos = data_get($item, 'statistics.videoCount');

            $yt->save();
            $ret->push($yt);
        }

        return $ret;
    }

    private static function chooseYoutubeThumbnail(object $snippet_thumbnail)
    {
        $keys = ['maxers', 'standard', 'high', 'medium', 'default'];
        foreach ($keys as $key) {
            $url = data_get($snippet_thumbnail, $key.'.url');
            if ($url) {
                return $url;
            }
        }
        return null;
    }
}
