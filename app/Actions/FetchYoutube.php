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

            $y = Youtube::firstOrNew(['code' => $key]);
            $y->code = data_get($item, 'id');
            $y->name = data_get($item, 'snippet.title');
            $y->description = data_get($item, 'snippet.description');
            $y->playlist = data_get($item, 'contentDetails.relatedPlaylists.uploads');
            $y->thumbnail_url = self::chooseYoutubeThumbnail(data_get($item, 'snippet.thumbnails'));
            $y->banner_url = data_get($item, 'brandingSettings.image.bannerTvHighImageUrl');

            $y->published_at = Util::UTCToLocalCarbon(data_get($item, 'snippet.publishedAt'));

            $y->views = data_get($item, 'statistics.viewCount');
            $y->comments = data_get($item, 'statistics.commentCount');
            $y->subscribers = data_get($item, 'statistics.subscriberCount');
            $y->videos = data_get($item, 'statistics.videoCount');

            $y->save();
            $ret->push($y);
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
