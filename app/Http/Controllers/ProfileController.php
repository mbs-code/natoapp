<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helper;
use App\Models\Profile;
use App\Models\ProfileTag;
use App\Rules\OrRules;
use App\Lib\Tasks\UpsertTwitterUser;
use App\Lib\Tasks\UpsertYoutubeChannel;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profiles = Profile::with(['twitters', 'youtubes', 'tags'])
            ->get()
            ->append('twitterFollowers')
            ->append('youtubeSubscribers')
            ->toArray();
        return Inertia::render('Profile/Index', ['profiles' => $profiles]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile)
    {
        $profile->load(['twitters', 'youtubes', 'tags']);
        return Inertia::render('Profile/Show', ['profile' => $profile]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->update($request, new Profile());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Profile $profile)
    {
        DB::transaction(function () use ($request, $profile)
        {
            // Profile
            $profile->fill(
                $request->validate([
                    // 'id' => ['required', ''],
                    'name' => ['required', 'max:100'],
                    'kana' => ['nullable', 'hiragana', 'max:100'],
                    'description' => ['nullable', 'max:65535'],
                    'thumbnail_url' => ['nullable', 'max:255'],
                ])
            );

            // profile props
            $props = $request->validate([
                'tags' => ['array'],
                'tags.*' => [new OrRules(['string', 'array'])],
                'tags.*.id' => ['sometimes', 'exists:App\Models\ProfileTag'], // ProfileTag のとき
                'twitters' => ['array'],
                'twitters.*' => [new OrRules(['string', 'array'])],
                'twitters.*.id' => ['sometimes', 'exists:App\Models\Twitter'], // Twitter のとき
                'youtubes' => ['array'],
                'youtubes.*' => [new OrRules(['string', 'array'])],
                'youtubes.*.id' => ['sometimes', 'exists:App\Models\Youtube'], // Youtube のとき
            ]);

            // tag
            $tagIDs = Helper::createSyncArray(data_get($props, 'tags'), function ($name) {
                return ProfileTag::firstOrCreate(['name' => $name ]);
            });

            // twitter
            $twitterIDs = Helper::createSyncArray(data_get($props, 'twitters'), function ($name) {
                $tw = UpsertTwitterUser::run($name);
                if ($tw->wasRecentlyCreated) {
                    $message = '「@'.$tw->name.'」を作成しました。';
                    Helper::messageFlash($message, 'success');
                }
                return $tw;
            });

            // youtube
            $youtubeIDs = Helper::createSyncArray(data_get($props, 'youtubes'), function ($id) {
                $yt = UpsertYoutubeChannel::run($id);
                if ($yt->wasRecentlyCreated) {
                    $message = '「'.$yt->name.'」を作成しました。';
                    Helper::messageFlash($message, 'success');
                }
                return $yt;
            });

            /// ////////////////////////////////////////

            $isChange = false;
            if ($profile->isDirty()) {
                $isChange = true;
                $profile->save();
            }
            // sync
            $syncTags = $profile->tags()->sync($tagIDs);
            $isChange |= Helper::syncChangeCount(($syncTags)) > 0;

            $syncTwitters = $profile->twitters()->sync($twitterIDs);
            $isChange |= Helper::syncChangeCount(($syncTwitters)) > 0;

            $syncYoutubes = $profile->youtubes()->sync($youtubeIDs);
            $isChange |= Helper::syncChangeCount(($syncYoutubes)) > 0;

            // message
            if ($isChange) {
                $method = $profile->wasRecentlyCreated ? '作成' : '編集';
                $message = '「'.$profile->name.'」を'.$method.'しました。';
                Helper::messageFlash($message, 'success');
            } else {
                Helper::messageFlash('変更点はありません。', 'info');
            }
        });

        return Redirect::back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        $name = $profile->name ?? 'プロファイル';
        $profile->delete();

        $message = '「'.$name.'」を削除しました。';
        Helper::messageFlash($message, 'success');
        return Redirect::back();
    }
}
