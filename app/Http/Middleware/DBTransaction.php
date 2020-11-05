<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

// ref: https://qiita.com/kd9951/items/da7326f74475823de10f
// ref: https://gist.github.com/aambrozkiewicz/5b38416fc84a824649b1a137bd4fa84c
class DBTransaction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        DB::beginTransaction();

        $response = $next($request);

        if ($response->exception) {
            DB::rollBack();
        } else {
            DB::commit();
        }

        return $response;
    }
}
