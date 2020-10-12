<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\ChannelInterface;
use App\Casts\CSV;
use App\Traits\HasHistoryModel;

class Video extends Model
{
    use HasFactory;
    use HasHistoryModel;

    protected $historyModel = VideoStat::class;

    protected $fillable = [
        'channel_id', 'channel_type','code', 'title', 'description', 'thumbnail_url',
        'type', 'status', 'duration', 'tags', 'max-viewers', 'published_at',
        'start_time', 'end_time', 'scheduled_start_time', 'scheduled_end_time',
        'actual_start_time', 'actual_end_time',
        'views', 'likes', 'dislikes', 'favorites', 'comments','concurrent_viewers',
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $casts = [
        'tags' => CSV::class,
    ];

    public function setChannelAttribute(ChannelInterface $channel)
    {
        $this->channel_type = 'App\Models\Youtube';
        $this->channel_id = $channel->id;
    }

    public function channel()
    {
        return $this->morphTo();
    }

    public function stats()
    {
        return $this->histories()->orderBy('created_at', 'desc')->limit(10);
    }
}
