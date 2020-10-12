<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeStat extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // disabled

    protected $fillable = [
        'views', 'comments', 'subscribers', 'videos',
    ];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function youtube()
    {
        return $this->belongsTo('App\Models\Youtube');
    }
}
