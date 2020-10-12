<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoStat extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // disabled

    protected $fillable = [
        'views', 'likes', 'dislikes', 'favorites', 'comments','concurrent_viewers',
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }
}
