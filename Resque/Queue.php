<?php
namespace PHPResqueBundle\Resque;

use Resque;
use Resque_Redis;

class Queue
{

    private static $backend = '';

    public function __construct($backend)
    {
        static::$backend = $backend;
    }

    public static function add($jobName, $queueName, $args = array())
    {
        Resque::setBackend(static::$backend);

        if (strpos($queueName, ':') !== false) {
            list($namespace, $queueName) = explode(':', $queueName);
            Resque_Redis::prefix($namespace);
        }

        try {
            $class = new \ReflectionClass($jobName);
            $jobId = Resque::enqueue($queueName, $class->getName(), $args, true);

            return $jobId;
        } catch (\ReflectionException $rfe) {
            throw new \RuntimeException($rfe->getMessage());
        }
    }
}
