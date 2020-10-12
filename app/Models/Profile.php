<?php

namespace App\Models;

class Profile extends BaseModel
{
    protected $fillable = [
        'name', 'kana', 'description', 'thumbnail_url', 'tags'
    ];

    /// ////////////////////////////////////////

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

    /// ////////////////////////////////////////

    public function twitters()
    {
        return $this->morphedByMany(Twitter::class, 'profilable')
            ->withPivot('created_at');
    }

    public function youtubes()
    {
        return $this->morphedByMany(Youtube::class, 'profilable')
            ->withPivot('created_at');
    }

    public function tags()
    {
        return $this->belongsToMany(ProfileTag::class, 'tag_profile')
            ->withPivot('created_at');
    }
}
