<?php

namespace App\Models;

use App\Traits\HasCRUDLogger;

class Profile extends BaseModel
{
    use HasCRUDLogger;

    protected $fillable = [
        'name', 'kana', 'description', 'tags',
        'thumbnail_url', 'published_at', 'followers', 'subscribes',
    ];

    public function getProfilablesAttribute()
    {
        // !!! readonly
        // TODO: 代替手段があったら書き換えたい
        return collect()
            ->merge($this->twitters)
            ->merge($this->youtubes);
    }

    public function twitters()
    {
        return $this->morphedByMany(Twitter::class, 'profilable')
            ->withPivot('created_at');
    }

    public function youtubes()
    {
        return $this->morphedByMany(Youtube::class, 'profilable')
            ->withPivot('created_at');
    }

    public function tags()
    {
        return $this->belongsToMany(ProfileTag::class, 'tag_profile')
            ->withPivot('created_at');
    }

    /// ////////////////////////////////////////

    public function cacheSync()
    {
        $pf = $this->profilables;
        $this->thumbnail_url = data_get($pf->first(fn($e) => $e->thumbnail_url), 'thumbnail_url');
        $this->published_at = data_get($pf->sortBy('published_at')->first(), 'published_at');

        $this->followers = collect($this->twitters, [])->max('followers');
        $this->subscribers = collect($this->youtubes, [])->max('subscribers');
        return $this;
    }

    public function __toString()
    {
        return "[{$this->id}] {$this->name}";
    }
}
