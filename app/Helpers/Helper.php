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

    /**
     * model sync 用の ID配列を作成する.
     *
     * [{ id: xx, name: asd }, zxc ] => [xx, <zxcID>]
     */
    public static function createSyncArray(array $items, callable $createCallback)
    {
        $ids = collect($items)->map(function ($item) use ($createCallback) {
            $id = data_get($item, 'id');
            if ($id === null) {
                $newItem = call_user_func($createCallback, $item);
                return data_get($newItem, 'id');
            }
            return $id;
        });
        return $ids;
    }

    /**
     * sync() の返り値からいくつ変更したかを返却する.
     */
    public static function syncChangeCount(array $syncs)
    {
        $attached = data_get($syncs, 'attached', []);
        $detached = data_get($syncs, 'detached', []);
        $updated = data_get($syncs, 'updated', []);
        return count($attached) + count($detached) + count($updated);
    }
}
