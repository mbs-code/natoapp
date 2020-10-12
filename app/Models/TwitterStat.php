<?php

namespace App\Models;

class TwitterStat extends BaseModel
{
    const UPDATED_AT = null; // disabled

    protected $fillable = [
        'followers', 'friends', 'listed', 'favourites', 'statuses',
    ];

    /// ////////////////////////////////////////

    public function twitter()
    {
        return $this->belongsTo(Twitter::class);
    }
}
