<?php

namespace App\Models;


class YoutubeStat extends BaseModel
{
    const UPDATED_AT = null; // disabled

    protected $fillable = [
        'views', 'comments', 'subscribers', 'videos',
    ];

    /// ////////////////////////////////////////

    public function youtube()
    {
        return $this->belongsTo(Youtube::class);
    }
}
