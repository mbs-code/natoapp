<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

// ref: https://qiita.com/j-yamada/items/096e0b04bb690da6651c
class OrRules implements Rule
{
    private $rules = [];

    function __construct(Array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * バリデーションの成功を判定
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($this->rules as $rule) {
            $validator = Validator::make([$attribute => $value], [
                $attribute => $rule
            ]);

            if (!$validator->fails()) {
                // エラーなしなら終了
                return true;
            }
        }
        return false;
    }

    /**
     * バリデーションエラーメッセージの取得
     *
     * @return string
     */
    public function message()
    {
        return ':attribute は正しい形で入力してください';
    }
}
