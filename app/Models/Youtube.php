<?php

namespace App\Models;

use App\Models\Interfaces\ChannelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Youtube extends Model implements ChannelInterface
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'playlist',
        'thumbnail_url', 'banner_url', 'published_at',
        'views', 'comments', 'subscribers', 'videos'];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
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
}
