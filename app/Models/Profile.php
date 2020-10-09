<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'kana', 'description', 'thumbnail_url', 'tags'];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getTwitterFollowersAttribute()
    {
        // use append('twitterFollowers')
        return collect($this->twitters, [])->max('followers');
    }

    public function getYoutubeSubscribersAttribute()
    {
        // use append('youtubeSubscribers')
        return collect($this->youtubes, [])->max('subscribers');
    }

    public function twitters()
    {
        return $this->morphedByMany('App\Models\Twitter', 'profilable')
            ->withPivot('created_at');
    }

    public function youtubes()
    {
        return $this->morphedByMany('App\Models\Youtube', 'profilable')
            ->withPivot('created_at');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\ProfileTag', 'tag_profile')
            ->withPivot('created_at');
    }
}
