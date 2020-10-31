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

    /**
     * 2つの文字列の最長一致部分を取り出す.
     */
    public static function chooseStringDiff(string $str1 = null, string $str2 = null)
    {
        $str1 = $str1 ?? '';
        $str2 = $str2 ?? '';

        $ary = str_split($str1); // 基準文字列配列
        $p = 0; // ポインタ
        $len = count($ary); // 比較元の配列

        // 1つ目の先頭から見ていく
        $word = '';
        $mostLengthWord = '';
        while ($p <= $len) {
            $wordBuf = $word; // 一致してた word のバッファ
            $word .= $p >= $len ? '' : $ary[$p]; // 一つ追加
            // echo('word: ['.$word.']'.PHP_EOL);

            if ($word) {
                if ((mb_strpos($str2, $word) === false || $p >= $len)) {
                    // word が2つ目に存在しないなら word を初期化 (最大長到達でも)
                    // もし最大長ならバッファに記録
                    // echo(mb_strlen($wordBuf).' '.mb_strlen($mostLengthWord).PHP_EOL);
                    if (mb_strlen($wordBuf) >= mb_strlen($mostLengthWord)) {
                        $mostLengthWord = $wordBuf;
                    }
                    $word = '';
                }
                // word が2つ目に存在するなら次の文字へ
            }

            $p ++;
        }

        return $mostLengthWord;
    }
}
