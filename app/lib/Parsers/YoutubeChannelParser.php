<?php

namespace App\Lib\Parsers;

use App\Lib\Parsers\Parser;
use App\Lib\TimeUtil;
use App\Models\Youtube;

class YoutubeChannelParser extends Parser
{
    public static function insert(object $item)
    {
        // youtube の生成
        $key = data_get($item, 'id');
        $yt = Youtube::firstOrNew(['code' => $key]);

        $yt->code = data_get($item, 'id');
        $yt->name = data_get($item, 'snippet.title');
        $yt->description = data_get($item, 'snippet.description');
        $yt->playlist = data_get($item, 'contentDetails.relatedPlaylists.uploads');
        $yt->thumbnail_url = self::chooseYoutubeThumbnail(data_get($item, 'snippet.thumbnails'));
        $yt->banner_url = data_get($item, 'brandingSettings.image.bannerTvHighImageUrl');

        $yt->tags = explode(' ', data_get($item, 'brandingSettings.channel.keywords'));
        $yt->published_at = TimeUtil::UTCToLocalCarbon(data_get($item, 'snippet.publishedAt'));

        $yt->views = data_get($item, 'statistics.viewCount');
        $yt->comments = data_get($item, 'statistics.commentCount');
        $yt->subscribers = data_get($item, 'statistics.subscriberCount');
        $yt->videos = data_get($item, 'statistics.videoCount');

        $yt->save();
        return $yt;
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
