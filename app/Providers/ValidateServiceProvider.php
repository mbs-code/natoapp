<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('hiragana', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[^ぁ-んー]/u', $value) === 0;
            return preg_match('/[ぁ-ん]+/u', $value);
        });
    }
}
