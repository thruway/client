<?php

namespace Thruway\Test;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface;

class TestLoop implements LoopInterface
{
    public function addReadStream($stream, callable $listener)
    {
        // TODO: Implement addReadStream() method.
    }

    public function addWriteStream($stream, callable $listener)
    {
        // TODO: Implement addWriteStream() method.
    }

    public function removeReadStream($stream)
    {
        // TODO: Implement removeReadStream() method.
    }

    public function removeWriteStream($stream)
    {
        // TODO: Implement removeWriteStream() method.
    }

    public function removeStream($stream)
    {
        // TODO: Implement removeStream() method.
    }

    public function addTimer($interval, callable $callback)
    {
        // TODO: Implement addTimer() method.
    }

    public function addPeriodicTimer($interval, callable $callback)
    {
        // TODO: Implement addPeriodicTimer() method.
    }

    public function cancelTimer(TimerInterface $timer)
    {
        // TODO: Implement cancelTimer() method.
    }

    public function isTimerActive(TimerInterface $timer)
    {
        // TODO: Implement isTimerActive() method.
    }

    public function nextTick(callable $listener)
    {
        // TODO: Implement nextTick() method.
    }

    public function futureTick(callable $listener)
    {
        // TODO: Implement futureTick() method.
    }

    public function tick()
    {
        // TODO: Implement tick() method.
    }

    public function run()
    {
        // TODO: Implement run() method.
    }

    public function stop()
    {
        // TODO: Implement stop() method.
    }
}