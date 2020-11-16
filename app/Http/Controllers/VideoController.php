<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helper;
use App\Models\Video;
use App\Tasks\Youtubes\UpsertYoutubeVideo;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = Video::with(['channel'])->get();
        return Inertia::render('Video/Index', ['videos' => $videos]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request)
        {
            // video を作成
            $props = $request->validate([
                // 'id' => ['required', ''],
                'code' => ['required', 'alpha_dash', 'max:100'],
            ]);

            $video = UpsertYoutubeVideo::run(data_get($props, 'code'));
            $message = '「'.$video->title.'」を作成しました。';
            Helper::messageFlash($message, 'success');
        });

        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $name = $video->title ?? 'Video';
        $video->delete();

        $message = '「'.$name.'」を削除しました。';
        Helper::messageFlash($message, 'success');
        return Redirect::back();
    }
}
