<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helper;
use App\Models\Profile;
use App\Models\ProfileTag;
use App\Rules\OrRules;
use Illuminate\Support\Facades\DB;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->edit(new Profile());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile)
    {
        $profile->load(['twitters', 'youtubes', 'tags']);
        $tags = ProfileTag::all();

        return Inertia::render('Profile/Edit', ['profile' => $profile, 'tags' => $tags]);
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
                    'description' => ['nullable', 'max:65535'],
                    'thumbnail_url' => ['nullable', 'max:255'],
                ])
            );
            $profile->save();

            // ProfileTag
            $tagProps = $request->validate([
                'tags' => ['array'],
                'tags.*' => [new OrRules(['string', 'array'])],
                'tags.*.id' => ['sometimes', 'exists:App\Models\ProfileTag'], // ProfileTag のとき
            ]);

            // tag id を抽出していく
            $tagIds = collect($tagProps['tags'])->map(function ($tag) {
                $tid = data_get($tag, 'id');
                if ($tid === null) {
                    // ID が無いなら作成する
                    $dbTag = ProfileTag::firstOrCreate(['name' => $tag ]);
                    $tid = $dbTag->id;
                }
                return $tid;
            });

            // sync tags
            $profile->tags()->sync($tagIds);
        });

        $message = $profile->wasRecentlyCreated
            ? 'プロファイルを更新しました。'
            : 'プロファイルを更新しました。';
        Helper::messageFlash($message, 'success');
        return Redirect::route('profiles.show', $profile);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
