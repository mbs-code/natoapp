<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // logger cli mode
        if(strpos(strtolower(php_sapi_name()), 'cli') !== false) {
            $path = 'logging.channels.stack.channels';
            $stacks = collect(config($path, []))
                ->push('stdout')
                ->unique();
            config([$path => $stacks]);
        }

        // error bag
        Inertia::share([
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ]);

        // inertia に渡したら session の値を消す
        Inertia::share('flash', function () {
            return [
                'toasts' => Session::pull('toasts'),
            ];
        });
    }
}
