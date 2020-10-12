<?php

namespace App\Models;

use App\Models\Interfaces\ChannelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\CSV;
use App\Traits\HasHistoryModel;

class Youtube extends Model implements ChannelInterface
{
    use HasFactory;
    use HasHistoryModel;

    protected $historyModel = YoutubeStat::class;
    protected $createHistoryWhenNoChanged = true;

    protected $fillable = [
        'code', 'name', 'description', 'playlist',
        'thumbnail_url', 'banner_url', 'tags', 'published_at',
        'views', 'comments', 'subscribers', 'videos'
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

    protected $appends = ['link'];

    public function getLinkAttribute()
    {
        $code = $this->code;
        return $code ? 'https://www.youtube.com/channel/'.$code : null;
    }

    public function profiles()
    {
        return $this->morphToMany('App\Models\Profile', 'profilable');
    }

    public function videos()
    {
        return $this->morphMany('App\Models\Video', 'channel');
    }

    public function stats()
    {
        return $this->histories()->orderBy('created_at', 'desc')->limit(10);
    }
}
