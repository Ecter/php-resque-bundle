<?php
namespace PHPResqueBundle\Resque;

use Resque_Event;

class Event
{

    public static function __callStatic($method, $args)
    {
        if (!static::isCallableEvent($method)) {
            if (strpos($method, 'Stop') !== false) {
                return static::stopEvent($method, $args);
            }

            throw new PHPResqueEventException("The {$method} does not exists");
        }

        if (!Resque_Event::listen($method, $args)) {
            throw new PHPResqueEventException("The {$method} cannot be registered.");
        }
    }

    public static function trigger($event, $data = null)
    {
        Resque_Event::trigger($event, $data);
    }

    public static function clearListeners()
    {
        Resque_Event::clearListeners();
    }

    private static function stopEvent($method, $args)
    {
        $method = str_replace("Stop", '', $method);
        if (! static::isCallableEvent($method)) {
            throw new PHPResqueEventException("The {$method} does not exists. So you can't stop it.");
        }

        if (! Resque_Event::stopListening($method, $args)) {
            throw new PHPResqueEventException("The {$method} can not stopped.");
        }
    }

    private static function isCallableEvent($method)
    {
        switch ($method) {
            case 'beforeFirstFork':
            case 'beforeFork':
            case 'afterFork':
            case 'beforePerform':
            case 'afterPerform':
            case 'onFailure':
            case 'afterEnqueue':
                return true;
            default :
                return false;
        }
    }
}
