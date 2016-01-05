<?php namespace PHPResqueBundle;

use Psr\Log\LoggerInterface;

class PHPResque
{

    private $queue = '*';
    private $logging = 'normal';
    private $checker_interval = 5;
    private $fork_count = 1;
    private $backend = '';
    private $stayAlive = false;
    private $blocking = true;

    /**
     * @var LoggerInterface logger
     */
    private $logger;

    public function __construct($backend, LoggerInterface $logger)
    {
        $this->backend = $backend;
        $this->logger = $logger;
    }

    public function defineQueue($name)
    {
        $this->queue = $name;
    }

    public function verbose($mode)
    {
        $this->logging = $mode;
    }

    public function setInterval($interval)
    {
        $this->checker_interval = (int) $interval;
    }

    /**
     * @param boolean $blocking
     */
    public function setBlocking($blocking)
    {
        $this->blocking = $blocking;
    }

    /**
     * @param boolean $stayAlive
     */
    public function setStayAlive($stayAlive)
    {
        $this->stayAlive = $stayAlive;
    }

    public function forkInstances($count)
    {
        settype($count, 'int');

        if ($count > 1) {
            if (function_exists('pcntl_fork')) {
                $this->fork_count = $count;
            } else {
                fwrite(STDOUT, "*** Fork could not initialized. PHP function pcntl_fork() does NOT exists \n");
                $this->fork_count = 1;
            }
        } else {
            $this->fork_count = 1;
        }
    }

    public function getForkInstances()
    {
        return $this->fork_count;
    }

    private function work()
    {
        $worker = new \Resque_Worker(explode(',', $this->queue));
        if ($this->logger instanceof LoggerInterface) {
            $worker->setLogger($this->logger);
        }
        if (method_exists($worker, 'setStayAlive')) {
            $worker->setStayAlive($this->stayAlive);
        }
        fwrite(STDOUT, '*** Starting worker ' . $worker . "\n");
        $worker->work($this->checker_interval, $this->blocking);
    }

    public function daemon()
    {
        \Resque::setBackend($this->backend);

        if (strpos($this->queue, ':') !== false) {
            list($namespace, $queue) = explode(':', $this->queue);
            \Resque_Redis::prefix($namespace);
            $this->queue = $queue;
        }

        if ($this->getForkInstances() > 1) {
            for ($i = 0; $i < $this->getForkInstances(); ++$i) {
                $pid = pcntl_fork();

                if ($pid == -1) {
                    throw new \RuntimeException("Could not fork worker {$i}");
                }

                $this->work();
                break;
            }
        } else {
            $this->work();
        }
    }
}
