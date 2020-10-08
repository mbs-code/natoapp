<?php

namespace App\Http\Controllers;

use App\Helpers\RequestQuerybuilder;
use Illuminate\Http\Request;
use App\Models\ProfileTag;
use App\Models\Twitter;
use App\Models\Youtube;
use App\Models\Video;

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

    public function videos(Request $request)
    {
        $videos = RequestQuerybuilder::builder(Video::query(), $request)
            ->ifWhereEqualIn('type')
            ->ifWhereHasMorph('channel')
            ->ifOrderBy('sort', 'order')
            ->ifPagination('page', 'limit')
            ->get();

        return $videos;
    }
}
