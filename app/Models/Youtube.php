<?php

namespace App\Models;

use App\Models\Interfaces\ChannelInterface;
use App\Traits\HasHistoryModel;
use App\Traits\HasCRUDLogger;
use App\Casts\CSV;

class Youtube extends BaseModel implements ChannelInterface
{
    use HasHistoryModel;
    use HasCRUDLogger;

    protected $historyModel = YoutubeStat::class;
    protected $createHistoryWhenNoChanged = true;

    protected $fillable = [
        'code', 'name', 'description', 'playlist',
        'thumbnail_url', 'banner_url', 'tags', 'published_at',
        'views', 'comments', 'subscribers', 'videos'
    ];

    public $casts = [
        'tags' => CSV::class,
    ];

    protected $appends = ['link'];

    /// ////////////////////////////////////////

    public function getLinkAttribute()
    {
        $code = $this->code;
        return $code ? 'https://www.youtube.com/channel/'.$code : null;
    }

    /// ////////////////////////////////////////

    public function profiles()
    {
        return $this->morphToMany(Profile::class, 'profilable');
    }

    public function videos()
    {
        return $this->morphMany(Video::class, 'channel');
    }

    public function stats()
    {
        return $this->histories()
            ->orderBy('created_at', 'desc')->limit(10);
    }

    /// ////////////////////////////////////////

    public function __toString()
    {
        return "[{$this->id}] {$this->name} ({$this->code})";
    }
}
