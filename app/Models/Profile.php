<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'thumbnail_url'];

    protected $dates = [
        'published_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function twitters()
    {
        return $this->morphedByMany('App\Models\Twitter', 'profilable')->withPivot('created_at');
    }

    public function channels()
    {
        return $this->morphedByMany('App\Models\Channel', 'profilable')->withPivot('created_at');
    }
}
