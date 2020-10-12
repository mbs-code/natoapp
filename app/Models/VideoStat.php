<?php

namespace App\Models;

class VideoStat extends BaseModel
{
    const UPDATED_AT = null; // disabled

    protected $fillable = [
        'views', 'likes', 'dislikes', 'favorites', 'comments','concurrent_viewers',
    ];

    /// ////////////////////////////////////////

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
