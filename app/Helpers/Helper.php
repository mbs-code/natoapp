<?php

namespace App\Helpers;

class Helper
{
    /**
     * session に flash message を決まった形で追加する.
     */
    public static function messageFlash(string $string, string $type)
    {
        $flash = array(
            "type" => $type,
            "text" => $string,
        );
        session()->push('toasts', $flash);
    }
}
