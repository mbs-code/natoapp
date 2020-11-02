<?php

namespace App\Models;

use App\Models\Interfaces\ChannelInterface;
use App\Traits\HasHistoryModel;
use App\Traits\HasCRUDLogger;
use App\Casts\CSV;

class Video extends BaseModel
{
    use HasHistoryModel;
    use HasCRUDLogger;

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
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'scheduled_start_time' => 'datetime',
        'scheduled_end_time' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
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
        $start = $this->start_time;
        if ($start) $start = $start->format('Ymd_His');

        $type = substr($this->type, 0, 4);
        return "[{$this->id}] {$this->start_time} ({$this->code}) [$type] {$this->title}";
    }
}
