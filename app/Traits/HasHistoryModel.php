<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use LogicException;

trait HasHistoryModel
{
    public static function bootHasHistoryModel()
    {
        self::created(function ($model) {
            $model->createHistoryRecord($model);
        });

        self::updated(function ($model) {
            $model->createHistoryRecord($model);
        });
    }

    public function histories()
    {
        return $this->hasMany($this->historyModel);
    }

    private function createHistoryRecord($model)
    {
        $historyModel = $this->historyModel;
        // 必ず Model 系を定義させる
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
}
