<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileTag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function profiles()
    {
        return $this->belongsToMany('App\Models\Profile', 'tag_profile')
            ->withPivot('created_at');
    }
}
