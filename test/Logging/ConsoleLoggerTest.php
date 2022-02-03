<?php

declare(strict_types=1);

namespace Logging;

use PHPUnit\Framework\TestCase;
use Thruway\Logging\ConsoleLogger;

class ConsoleLoggerTest extends TestCase
{
    /**
     * @dataProvider logLevels
     */
    public function testLog($level)
    {
        $logger = new ConsoleLogger();

        $expectedOutput = "\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}.\d{7}\s+$level\s+Hello World";
        $this->expectOutputRegex("/^$expectedOutput$/");

        $logger->$level('Hello World');
    }

    public function logLevels()
    {
        return [
            ['emergency'],
            ['alert'],
            ['critical'],
            ['error'],
            ['warning'],
            ['notice'],
            ['info'],
            ['debug'],
        ];
    }
}