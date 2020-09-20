<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Twitter extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'name', 'screen_name', 'location', 'description',
        'url', 'thumbnail_url', 'banner_url', 'protected', 'published_at',
        'followers', 'friends', 'listed', 'favourites', 'statuses', 'last_tweet_id'];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
