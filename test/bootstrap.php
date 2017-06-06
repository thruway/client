<?php

use Psr\Log\NullLogger;
use Thruway\Logging\Logger;

if (file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    $loader = require $file;
    $loader->addPsr4('Thruway\\', __DIR__);
} else {
    throw new RuntimeException('Install dependencies to run test suite.');
}

Logger::set(new NullLogger());
