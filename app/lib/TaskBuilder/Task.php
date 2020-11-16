<?php

namespace App\Lib\TaskBuilder;

use App\Lib\TaskBuilder\Events\TaskEventer;
use App\Lib\TaskBuilder\Utils\EventManager;
use App\Lib\TaskBuilder\Utils\TaskFlow;
use App\Lib\TaskBuilder\Jobs\BaseJob;

class Task
{

    private TaskFlow $flow;
    private EventManager $event;

    public function __construct(EventManager $manager = null)
    {
        // 親要素(root) は $manager = null、子要素は引き継ぐ
        $this->event = $manager ?? new EventManager();
        $this->flow = new TaskFlow();
    }

    ///
    // 実行本体

    // 外部実行用
    public function exec($value, TaskEventer $e)
    {
        // 記録用の manager を付与
        $e->setEventManager($this->event);
        $e->write('timestamp', microtime(true));

        // Task 実行
        $e->fireEvent('before task', $value);
        $res = $this->handle($value, $e);
        $e->fireEvent('after task', $res);

        return $res;
    }

    // !!! lib内部実行用 (Eventer を引き継ぐ)
    // arg は loop のキーとかを入れる用
    public function handle($value, TaskEventer $e, $arg = null)
    {
        // iterator の取得
        $it = $this->flow->getIterator();
        $it->rewind();

        // flow を順番に実行する
        $buffer = $value; // コンテナ (置換)
        while ($it->valid()) {
            $job = $it->current();

            // タスクの実行
            $res = $job->call($buffer, $e, $arg);
            $buffer = $res;

            // 次へ
            $it->next();
        }

        return $buffer;
    }

    ///
    // builder function

    public function addJob(BaseJob $job)
    {
        $this->flow->add($job);
    }

    public function addEvent(string $name, callable $fireFunc)
    {
        $this->event->addEvent($name, $fireFunc);
    }

    public function isMute(bool $isMute = true) {
        $this->event->isMute($isMute);
    }

}
