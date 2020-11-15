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

    public function getEventJob(int $parent = 0)
    {
        // 現在のを取り出す
        $key = count($this->jobs) - 1 - $parent;
        if ($key > 0) {
            return $this->jobs[$key];
        }
        return null;
    }

    public function getJobName(int $parent = 0)
    {
        $job = $this->getEventJob($parent);
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
