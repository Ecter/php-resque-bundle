<?php
namespace PHPResqueBundle\Resque;

use Resque;
use Resque_Redis;
use Resque_Job_Status;

class Status {

    private static $backend = '';

    public function __construct($backend) {
        static::$backend = $backend;
    }

    public static function check($jobId, $namespace) {
        Resque::setBackend(static::$backend);

        if (!empty($namespace)) {
            Resque_Redis::prefix($namespace);
        }

        $status = new Resque_Job_Status($jobId);
        if (!$status->isTracking()) {
            die("Resque is not tracking the status of this job.\n");
        }

        $class = new \ReflectionObject($status);

        foreach ($class->getConstants() as $constantName => $constantValue) {
            if ($constantValue == $status->get()) {
                return 'Job status in queue is ' . $status->get() . " [$constantName]";
            }
        }
    }

    public static function update($status, $toJobId, $namespace) {
        Resque::setBackend(static::$backend);

        if (!empty($namespace)) {
            Resque_Redis::prefix($namespace);
        }

        $job = new Resque_Job_Status($toJobId);

        if (!$job->get()) {
            throw new \RuntimeException("Job {$toJobId} was not found");
        }

        $class = new \ReflectionObject($job);

        foreach ($class->getConstants() as $constantValue) {
            if ($constantValue == $status) {
                $job->update($status);
                return true;
            }
        }

        return false;
    }
}