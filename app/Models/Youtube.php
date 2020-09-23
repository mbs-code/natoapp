<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Youtube extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'playlist',
        'thumbnail_url', 'banner_url', 'published_at',
        'views', 'comments', 'subscribers', 'videos'];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function profiles()
    {
        return $this->morphToMany('App\Models\Profile', 'profilable');
    }
}
