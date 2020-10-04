<?php

namespace App\Http\Controllers;

use App\Models\ProfileTag;
use App\Models\Twitter;
use App\Models\Youtube;

class APIController extends Controller
{
    public function tags()
    {
        $tags = ProfileTag::select(['id', 'name'])->get();
        return $tags;
    }

    public function twitters()
    {
        $twitters = Twitter::select(['id', 'name'])->get();
        return $twitters;
    }

    public function youtubes()
    {
        $youutbes = Youtube::select(['id', 'name'])->get();
        return $youutbes;
    }
}
