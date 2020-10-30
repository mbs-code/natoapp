<?php

namespace App\Lib\Parsers;

use App\Lib\Parsers\Parser;
use App\Lib\TimeUtil;
use App\Exceptions\NoChannelException;
use App\Exceptions\NullPointerException;
use App\Lib\Tasks\UpsertYoutubeChannel;
use App\Models\Youtube;
use App\Models\Video;
use App\Enums\VideoStatus;
use App\Enums\VideoType;

class YoutubeVideoParser extends Parser
{

    public static function delete(string $key)
    {
        $vv = Video::where(['code' => $key])->first();
        if (!$vv) {
            // 無いなら例外
            throw new NullPointerException('videoId = '.$key);
        }

        $vv->status = VideoStatus:: DELETE(); // とりあえず delete に
        // TODO: private, BAN との判別方法がほしい

        // ライブ中だったら終わった時刻をとりあえず入れておく
        if (VideoType::LIVE()->equals($vv->type)) {
            $vv->type = VideoType::ARCHIVE();
            $vv->endTime = TimeUtil::LocaleCarbonNow();
        }

        $vv->save();
        return $vv;
    }

    public static function insert(object $item, bool $createNewChannel, bool $skipNewChannel)
    {
        // channel の取得
        $channelID = data_get($item, 'snippet.channelId');
        $channel = Youtube::where(['code' => $channelID])->first();
        if (!$channel) {
            // 無いなら作成かスキップか例外
            if ($createNewChannel) {
                $channel = UpsertYoutubeChannel::run($channelID);
            } else if ($skipNewChannel) {
                return false;
            } else {
                throw new NoChannelException('channelId = '.$channelID);
            }
        }

        // video の生成
        $key = data_get($item, 'id');
        $vv = Video::firstOrNew(['code' => $key]);
        $vv->channel = $channel;

        $vv->code = data_get($item, 'id');
        $vv->title = data_get($item, 'snippet.title');
        $vv->description = data_get($item, 'snippet.description');
        $vv->thumbnail_url = self::chooseYoutubeThumbnail(data_get($item, 'snippet.thumbnails'));
        $vv->duration = TimeUtil::parseDuration(data_get($item, 'contentDetails.duration'));

        $vv->tags = data_get($item, 'snippet.tags');
        $vv->published_at = TimeUtil::UTCToLocalCarbon(data_get($item, 'snippet.publishedAt'));

        $vv->scheduled_start_time = TimeUtil::UTCToLocalCarbon(data_get($item, 'liveStreamingDetails.scheduledStartTime'));
        $vv->scheduled_end_time = TimeUtil::UTCToLocalCarbon(data_get($item, 'liveStreamingDetails.scheduledEndTime'));
        $vv->actual_start_time = TimeUtil::UTCToLocalCarbon(data_get($item, 'liveStreamingDetails.actualStartTime'));
        $vv->actual_end_time = TimeUtil::UTCToLocalCarbon(data_get($item, 'liveStreamingDetails.actualEndTime'));

        $vv->views = data_get($item, 'statistics.viewCount');
        $vv->likes = data_get($item, 'statistics.likeCount');
        $vv->dislikes = data_get($item, 'statistics.dislikeCount');
        $vv->favorites = data_get($item, 'statistics.favoriteCount');
        $vv->comments = data_get($item, 'statistics.commentCount');
        $vv->concurrent_viewers = data_get($item, 'liveStreamingDetails.concurrentViewers');

        $vv->type = self::calcType($vv);
        $vv->status = self::parseStatus(data_get($item, 'status.privacyStatus'));

        $startEnd = self::calcStartEndTime($vv); // type 必須
        $vv->start_time = $startEnd['start'];
        $vv->end_time = $startEnd['end'];

        $vv->save();
        return $vv;
    }

    /// ////////////////////////////////////////////////////////////

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

    private static function calcType(Video $video)
    {
        if ($video->actual_end_time) {
            // 終了時刻があったらアーカイブ (公開日時が同じならプレミア)
            if ($video->actual_end_time->equalTo($video->published_at)) {
                return VideoType::PREMIERE();
            }
            return VideoType::ARCHIVE();
        } else if ($video->actual_start_time) {
            // 開始時刻があったら配信中
            return VideoType::LIVE();
        } else if ($video->scheduled_start_time) {
            // 予定時刻があったら待機中
            return VideoType::UPCOMING();
        }
        // それ以外はただの動画
        return VideoType::VIDEO();
    }

    private static function parseStatus(string $privacyStatus)
    {
        if ($privacyStatus === 'public') {
            return VideoStatus::PUBLIC();
        } else if ($privacyStatus === 'unlisted') {
            return VideoStatus::UNLISTED();
        } else if ($privacyStatus === 'private') {
            return VideoStatus:: PRIVATE();
        }
    }

    private static function calcStartEndTime (Video $video)
    {
        if (VideoType::ARCHIVE()->equals($video->type)) {
            // アーカイブなら 開始時刻 => 終了時刻
            return [
                'start' => $video->actual_start_time,
                'end' => $video->actual_end_time,
            ];
        } else if (VideoType::LIVE()->equals($video->type)) {
            // 配信中なら 開始時刻 => 現在時刻
            return [
                'start' => $video->actual_start_time,
                'end' => TimeUtil::LocaleCarbonNow(),
            ];
        } else if (VideoType::UPCOMING()->equals($video->type)) {
            // 配信予約中なら 予定時刻 => +1min
            $start = $video->scheduled_start_time->copy();
            return [
                'start' => $video->scheduled_start_time,
                'end' => $start->addMinute(),
            ];
        } else if (VideoType::VIDEO()->equals($video->type)) {
            // 動画なら 投稿時間 => +duration
            $published = $video->published_at->copy();
            return [
                'start' => $video->published_at,
                'end' => $published->addMinutes($video->duration),
            ];
        }
    }
}
