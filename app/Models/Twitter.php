<?php

namespace App\Models;

use App\Traits\HasHistoryModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Twitter extends Model
{
    use HasFactory;
    use HasHistoryModel;

    protected $historyModel = TwitterStat::class;
    protected $createHistoryWhenNoChanged = true;

    protected $fillable = [
        'code', 'name', 'screen_name', 'location', 'description',
        'url', 'thumbnail_url', 'banner_url', 'protected', 'published_at',
        'followers', 'friends', 'listed', 'favourites', 'statuses', 'last_tweet_id'];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'protected' => 'boolean',
    ];

    protected $appends = ['link'];

    public function getLinkAttribute()
    {
        $screenName = $this->screen_name;
        return $screenName ? 'https://twitter.com/'.$screenName : null;
    }

    public function profiles()
    {
        return $this->morphToMany('App\Models\Profile', 'profilable');
    }

    public function stats()
    {
        return $this->histories()->orderBy('created_at', 'desc')->limit(10);
    }
}
