<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwitterStat extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // disabled

    protected $fillable = [
        'followers', 'friends', 'listed', 'favourites', 'statuses',
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function twitter()
    {
        return $this->belongsTo('App\Models\Twitter');
    }
}
