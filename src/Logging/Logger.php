<?php

namespace Thruway\Logging;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger
{

    /**
     * @var LoggerInterface
     */
    private static $logger;

    /**
     * @param LoggerInterface $logger
     */
    public static function set(LoggerInterface $logger)
    {
        static::$logger = $logger;
    }

    /**
     * @param object|null $object
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function log($object, $level, $message, $context = [])
    {
        if (is_object($object)) {
            $className = get_class($object);
            $pid       = getmypid();
            $message   = "[{$className} {$pid}] {$message}";
        }

        if (static::$logger === null) {
            static::$logger = new ConsoleLogger();
        }

        static::$logger->log($level, $message, $context);
    }

    /**
     * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function alert($object, $message, $context = [])
    {
        static::log($object, LogLevel::ALERT, $message, $context);
    }

    /**
     * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function critical($object, $message, $context = [])
    {
        static::log($object, LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function debug($object, $message, $context = [])
    {
        static::log($object, LogLevel::DEBUG, $message, $context);
    }

    /**
     * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function emergency($object, $message, $context = [])
    {
        static::log($object, LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error($object, $message, $context = [])
    {
        static::log($object, LogLevel::ERROR, $message, $context);
    }

    /**
     * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info($object, $message, $context = [])
    {
        static::log($object, LogLevel::INFO, $message, $context);
    }

    /**
     * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function notice($object, $message, $context = [])
    {
        static::log($object, LogLevel::NOTICE, $message, $context);
    }

    /**
	 * @param object|null $object
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning($object, $message, $context = [])
    {
        static::log($object, LogLevel::WARNING, $message, $context);
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    public function __wakeup()
    {
    }
}
