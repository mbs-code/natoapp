<?php

namespace App\Traits;

use App\Helpers\Helper;

trait HasCRUDLogger
{
    public static function bootHasCRUDLogger()
    {
        self::created(function ($model) {
            $modelName = class_basename($model);
            $str = (string) $model;
            logger()->notice("E:Created {$modelName}: {$str}");

            if (self::isBrowser()) {
                $message = "{$modelName}「{$str}」を作成しました。";
                Helper::messageFlash($message, 'success');
            }
        });

        self::deleted(function ($model) {
            $modelName = class_basename($model);
            $str = (string) $model;
            logger()->notice("E:Deleted {$modelName}: {$str}");

            if (self::isBrowser()) {
                $message = "{$modelName}「{$str}」を削除しました。";
                Helper::messageFlash($message, 'success');
            }
        });

        self::saved(function ($model) {
            $modelName = class_basename($model);
            $str = (string) $model;
            // save 時はログに書き出さない

            if (self::isBrowser()) {
                $message = "{$modelName}「{$str}」を変更しました。";
                Helper::messageFlash($message, 'success');
            }
        });
    }

    private static function isBrowser()
    {
        $sapi = strtolower(php_sapi_name());
        return (strpos($sapi, 'apache') !== false);
    }
}
