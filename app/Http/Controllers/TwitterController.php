<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helper;
use App\Models\Twitter;
use App\Lib\Tasks\UpsertTwitterUser;

class TwitterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $twitters = Twitter::with(['stats'])->get();
        return Inertia::render('Twitter/Index', ['twitters' => $twitters]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, new Twitter());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Twitter  $twitter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Twitter $twitter)
    {
        DB::transaction(function () use ($request, $twitter)
        {
            // twitter
            $twitter->fill(
                $request->validate([
                    // 'id' => ['required', ''],
                    'screen_name' => ['required', 'max:255'],
                ])
            );

            if (!$twitter->isDirty()) {
                Helper::messageFlash('変更点がありません。', 'info');
            } else {
                if ($twitter->id) {
                    $twitter->save();
                }

                // api からデータを作成する
                $twitter = UpsertTwitterUser::run($twitter->screen_name);

                $method = $twitter->wasRecentlyCreated ? '作成' : '編集';
                $message = '「@'.$twitter->screen_name.'」を'.$method.'しました。';
                Helper::messageFlash($message, 'success');
            }
        });

        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Twitter  $twitter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Twitter $twitter)
    {
        $name = $twitter->screen_name ?? 'Twitter';
        $twitter->delete();

        $message = '「@'.$name.'」を削除しました。';
        Helper::messageFlash($message, 'success');
        return Redirect::back();
    }
}
