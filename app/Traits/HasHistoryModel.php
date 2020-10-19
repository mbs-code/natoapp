<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use LogicException;

trait HasHistoryModel
{
    // 保存するモデル
    // protected $historyModel = YoutubeStat::class;

    // 一つも変更が無くても保存する
    // protected $createHistoryWhenNoChanged = false;

    public static function bootHasHistoryModel()
    {
        self::created(function ($model) {
            $model->createHistoryRecord($model);
        });

        self::updated(function ($model) {
            $model->createHistoryRecord($model);
        });

        // 何も変更が無い時の routing
        self::saved(function ($model) {
            if ($model->createHistoryWhenNoChanged) {
                $model->createHistoryRecord($model);
            }
        });

        // DB 削除時に history も削除する（残す意味ないしね）
        if (method_exists(static::class, 'bootSoftDeletes')) {
            self::forceDeleted(function ($model) {
                $model->removeHistoryRecord($model);
            });
        } else {
            self::deleted(function ($model) {
                $model->removeHistoryRecord($model);
            });
        }
    }

    public function histories()
    {
        return $this->hasMany($this->historyModel);
    }

    private function createHistoryRecord($model)
    {
        // 必ず Model 系を定義させる
        $historyModel = $this->historyModel;
        if (!$historyModel || !is_subclass_of($historyModel, Model::class)) {
            throw new LogicException('Wrong implementation of "$this->historyModel"');
        }

        // original: 更新前
        // attributes: 更新後

        $hm = new $historyModel();
        $fills = collect($hm->getFillable())
            ->mapWithKeys(function ($key) use ($model) {
                return [$key => $model->getAttribute($key)];
            })
            ->toArray();

        $hm->fill($fills);
        $model->histories()->save($hm);
    }

    private function removeHistoryRecord($model)
    {
        $model->histories()->delete();
    }
}
