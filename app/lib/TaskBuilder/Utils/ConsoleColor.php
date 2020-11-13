<?php

namespace App\Lib\TaskBuilder\Utils;

use Bramus\Ansi\Ansi;
use Exception;
use Illuminate\Support\Str;

// doc: https://github.com/bramus/ansi-php
class ConsoleColor extends Ansi
{
    private static $instance;

    public static function console()
    {
        return self::$instance ?? new static();
    }

    ///

    private $limitLineLength = 0; // 一行あたりの文字数
    private $length = 0; // 現在の文字数

    public function setLineLength(int $length) {
        $this->limitLineLength = $length;
    }

    ///

    public function print($val, bool $limit = false)
    {
        $text = $this->stringFormat($val, $limit);
        return $this->text($text);
    }

    public function text($val)
    {
        $len = strlen($val);
        $this->length += $len;
        return parent::text($val);
    }

    public function lf()
    {
        $this->length = 0;
        return parent::lf()->noStyle();
    }

    public function br()
    {
        // alias
        return $this->lf();
    }

    ///

    private function stringFormat($text, bool $limit = false)
    {
        // null だったら文字列にする
        if ($text === null) {
            $text = 'null';
        }

        // json 形式に (配列は toString() を使う)
        if ($text instanceof Exception) {
            $text = (string) $text;
        } else if (is_object($text) || is_array($text)) {
            $text = $this->toJsonString($text);
        }

        // 文字数制限
        if ($limit && $this->limitLineLength > 0) {
            $text = $this->strLimit($text);
        }

        return $text;
    }

    private function strLimit($text)
    {
        // -3 は ... の分
        $limit = $this->limitLineLength - $this->length - 3;
        return Str::of($text)->limit($limit, '...');
    }

    private function toJsonString($val)
    {
        return json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
