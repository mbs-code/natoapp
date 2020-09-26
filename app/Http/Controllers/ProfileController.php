<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\Helper;
use App\Models\Profile;

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
        return Inertia::render('Profile/Edit');
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
        return Inertia::render('Profile/Edit', ['profile' => $profile]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $profile = Profile::create(
            FacadeRequest::validate([
                'name' => ['required', 'max:100'],
                'description' => ['nullable', 'max:65535'],
                'thumbnail_url' => ['nullable', 'max:255'],
            ])
        );
        return Redirect::route('profiles.show', $profile);
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
        $profile->update(
            FacadeRequest::validate([
                'name' => ['required', 'max:100'],
                'description' => ['nullable', 'max:65535'],
                'thumbnail_url' => ['nullable', 'max:255'],
            ])
        );

        Helper::messageFlash('プロファイルを更新しました。', 'success');
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
