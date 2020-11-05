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
        $profiles = Profile::with(['twitters', 'youtubes', 'tags'])->get();
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
                return UpsertTwitterUser::run($name)->first();
            });

            // youtube
            $youtubeIDs = Helper::createSyncArray(data_get($props, 'youtubes'), function ($id) {
                return UpsertYoutubeChannel::run($id)->first();
            });

            /// ////////////////////////////////////////

            $isChange = false;
            if ($profile->isDirty()) {
                $isChange = true;
                $profile->save();
            }
            // sync
            $syncTags = $profile->tags()->sync($tagIDs);
            $isChange |= Helper::syncChangeCount($syncTags) > 0;

            $syncTwitters = $profile->twitters()->sync($twitterIDs);
            $isChange |= Helper::syncChangeCount($syncTwitters) > 0;

            $syncYoutubes = $profile->youtubes()->sync($youtubeIDs);
            $isChange |= Helper::syncChangeCount($syncYoutubes) > 0;

            // cache
            $profile->cacheSync()->save();

            // message
            if (!$isChange) {
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
    public function destroy(Request $request, Profile $profile)
    {
        DB::transaction(function () use ($request, $profile)
        {
            $props = $request->validate([
                'withProfilable' => ['nullable', 'boolean'],
            ]);

            // 関連オブジェクトの削除
            if ($props['withProfilable']) {
                // profilable 要素とそれから逆向きの profile を取得
                $profile->load(['twitters', 'youtubes', 'twitters.profiles', 'youtubes.profiles']);
                foreach ($profile->twitters as $twitter) {
                    // 逆向きに profile が 1つ(自身)なら削除
                    if ($twitter->profiles->count() === 1) {
                        $twitter->delete();
                    }
                }
                foreach ($profile->youtubes as $youtube) {
                    // 逆向きに profile が 1つ(自身)なら削除
                    if ($youtube->profiles->count() === 1) {
                        $youtube->delete();
                    }
                }
            }

            // 削除
            $profile->delete();
        });

        return Redirect::back();
    }
}
