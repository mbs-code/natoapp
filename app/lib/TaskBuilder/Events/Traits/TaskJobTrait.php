<?php

namespace App\Lib\TaskBuilder\Events\Traits;

use App\Lib\TaskBuilder\Jobs\BaseJob;

trait TaskJobTrait
{
    private $jobs = []; // job stack

    public function pushEventJob(BaseJob $job)
    {
        array_push($this->jobs, $job);
    }

    public function popEventJob()
    {
        $job = array_pop($this->jobs);
        return $job;
    }

    public function getEventJob()
    {
        // 現在のを取り出す
        $last = array_key_last($this->jobs);
        if ($last !== null) {
            return $this->jobs[$last];
        }
        return null;
    }

    public function getJobName()
    {
        $job = $this->getEventJob();
        if ($job) {
            return $job->getName();
        }
        return null;
    }

    public function getJobNestLevel()
    {
        return count($this->jobs);
    }
}
