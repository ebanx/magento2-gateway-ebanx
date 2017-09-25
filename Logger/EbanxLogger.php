<?php
namespace Ebanx\Payments\Logger;

use Monolog\Logger;

class EbanxLogger extends Logger
{

    /**
     * Detailed debug information
     */
    const EBANX_DEBUG = 101;
    const EBANX_NOTIFICATION = 201;
    const EBANX_RESULT = 202;

    /**
     * Logging levels from syslog protocol defined in RFC 5424
     * Overrule the default to add Ebanx specific loggers to log into seperate files
     *
     * @var array $levels Logging levels
     */
    protected static $levels = [
        100 => 'DEBUG',
        101 => 'EBANX_DEBUG',
        200 => 'INFO',
        201 => 'EBANX_NOTIFICATION',
        202 => 'EBANX_RESULT',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    ];

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addEbanxNotification($message, array $context = array())
    {
        return $this->addRecord(static::EBANX_NOTIFICATION, $message, $context);
    }

    public function addEbanxDebug($message, array $context = array())
    {
        return $this->addRecord(static::EBANX_DEBUG, $message, $context);
    }

    public function addEbanxResult($message, array $context = array())
    {
        return $this->addRecord(static::EBANX_RESULT, $message, $context);
    }

    /**
     * Adds a log record.
     *
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addRecord($level, $message, array $context = [])
    {
        $context['is_exception'] = $message instanceof \Exception;
        return parent::addRecord($level, $message, $context);
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return Boolean Whether the record has been processed
     */
    public function addNotificationLog($message, array $context = array())
    {
        return $this->addRecord(static::INFO, $message, $context);
    }
}