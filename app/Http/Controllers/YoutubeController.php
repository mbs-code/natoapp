<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helper;
use App\Models\Youtube;
use App\Actions\FetchYoutube;

class YoutubeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $youtubes = Youtube::all();
        return Inertia::render('Youtube/Index', ['youtubes' => $youtubes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, new Youtube());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Youtube  $youtube
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Youtube $youtube)
    {
        DB::transaction(function () use ($request, $youtube)
        {
            // youtube
            $youtube->fill(
                $request->validate([
                    // 'id' => ['required', ''],
                    'code' => ['required', 'max:255'],
                ])
            );

            if (!$youtube->isDirty()) {
                Helper::messageFlash('変更点がありません。', 'info');
            } else {
                if ($youtube->id) {
                    $youtube->save();
                }

                // api からデータを作成する
                $youtube = FetchYoutube::handle([$youtube->code])->first();

                $method = $youtube->wasRecentlyCreated ? '作成' : '編集';
                $message = '「'.$youtube->name.'」を'.$method.'しました。';
                Helper::messageFlash($message, 'success');
            }
        });

        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Youtube  $youtube
     * @return \Illuminate\Http\Response
     */
    public function destroy(Youtube $youtube)
    {
        $name = $youtube->name ?? 'Youtube';
        $youtube->delete();

        $message = '「'.$name.'」を削除しました。';
        Helper::messageFlash($message, 'success');
        return Redirect::back();
    }
}
