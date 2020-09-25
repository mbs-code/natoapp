<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use App\Helpers\Helper;

class DebugController extends Controller
{
    /**
     * Append toast.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toast(Request $request)
    {
        $toast = Request::validate([
            'message' => ['required', 'max:100'],
            'type' => ['nullable', Rule::in(['info', 'success', 'error'])],
        ]);

        Helper::messageFlash($toast['message'], $toast['type'] ?? 'info');
        return Redirect::back();
    }
}
