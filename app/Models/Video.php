<?php

namespace App\Models;

use App\Models\Interfaces\ChannelInterface;
use App\Traits\HasHistoryModel;
use App\Casts\CSV;

class Video extends BaseModel
{
    use HasHistoryModel;

    protected $historyModel = VideoStat::class;
    protected $createHistoryWhenNoChanged = true;

    protected $fillable = [
        'channel_id', 'channel_type','code', 'title', 'description', 'thumbnail_url',
        'type', 'status', 'duration', 'tags', 'max-viewers', 'published_at',
        'start_time', 'end_time', 'scheduled_start_time', 'scheduled_end_time',
        'actual_start_time', 'actual_end_time',
        'views', 'likes', 'dislikes', 'favorites', 'comments','concurrent_viewers',
    ];

    public $casts = [
        'tags' => CSV::class,
    ];

    /// ////////////////////////////////////////

    public function setChannelAttribute(ChannelInterface $channel)
    {
        $this->channel_type = Youtube::class;
        $this->channel_id = $channel->id;
    }

    /// ////////////////////////////////////////

    public function channel()
    {
        return $this->morphTo();
    }

    public function stats()
    {
        return $this->histories()
            ->orderBy('created_at', 'desc')->limit(10);
    }

    /// ////////////////////////////////////////

    public function __toString()
    {
        return "[{$this->id}] {$this->title}";
    }
}
