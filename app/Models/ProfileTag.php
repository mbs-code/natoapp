<?php

namespace App\Models;

class ProfileTag extends BaseModel
{
    protected $fillable = ['name', 'color'];

    /// ////////////////////////////////////////

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'tag_profile')
            ->withPivot('created_at');
    }
}
