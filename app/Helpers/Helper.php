<?php

namespace App\Helpers;

class Helper
{
    /**
     * session に flash message を決まった形で追加する.
     *
     * vue への受け渡しは App/Providers/AppServideProvider
     */
    public static function messageFlash(string $message, string $type)
    {
        $flash = array(
            "type" => $type, // info, success, error
            "message" => $message,
        );
        session()->push('toasts', $flash);
    }

    public static function arrayDiffDirect($a, $b) {
        $ab = collect($a)->diff($b);
        $ba = collect($b)->diff($a);
        return $ab->merge($ba);
    }
}
