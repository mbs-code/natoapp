<?php

namespace App\Lib\Tasks\Bases;

use App\Lib\Tasks\Events\EventAttrs;

/**
 * preFormat() -> handle() -> postFormat()
 *
 * <event> attribute:
 * - <beforeExec>: exec 前処理
 * - <beforeProcess>: process 前処理、preFormat 実行後
 * - <processed>: process 後処理
 * - <execed>: exec 後処理、postFormat 実行後
 *   - execProps: process 前の値
 *   - processProps: process 前の値
 *   - processResponse: process 後の値
 *   - execResponse: exec 後の値
 *
 *   - startTimestamp: 開始時刻
 */
abstract class Task extends BaseTask
{
    /**
     * メイン処理.
     *
     * @param  mixed $var
     * @return mixed false で失敗扱い
     */
    protected abstract function handle($data);

    protected function preFormat($var)
    {
        return $var;
    }

    protected function postFormat($var)
    {
        return $var;
    }

    /// ////////////////////////////////////////

    public function exec($var)
    {
        $e = $this->bindEventAttrs(); // 記録開始

        $e->startTimestamp = microtime(true);
        $e->execProps = $var;

        // 引数の事前整形
        $this->fireEvent('beforeExec', $e);
        $e->processProps = $this->preFormat($e->execProps);

        // ■ start process
        $this->fireEvent('beforeProcess', $e);
        $process = $this->process($e->processProps, $e);

        $e->processResponse = $process;
        $this->fireEvent('processed', $e);
        // ■ end process

        // 戻り値の整形
        $e->execResponse = $this->postFormat($e->processResponse);
        $this->fireEvent('execed', $e);

        $res = $e->execResponse;
        $this->unbindEventAttrs(); // 記録消去

        return $res;
    }

    /// ////////////////////////////////////////

    protected function process($data, EventAttrs $e)
    {
        $e->handleProps = $data;

        // ■ start handle
        $this->fireEvent('beforeHandle', $e);

        $handle = $this->handle($e->handleProps, $e);
        $e->handleResponse = $handle;

        $this->fireEvent('handled', $e);
        // ■ end handle

        return $handle;
    }
}
