<?php
/**
 *
 * @author dknx01 <e.witthauer@gmail.com>
 * @since 10.08.13 15:55
 * @package
 *
 */

namespace Logger;

use \Logger\Writer\WriterAbstract;

class Logger
{
    /**
     * @const int defined from the BSD Syslog message severities
     * @link http://tools.ietf.org/html/rfc3164
     */
    const EMERG = 0;
    const ALERT = 1;
    const CRIT = 2;
    const ERR = 3;
    const WARN = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;
    /**
     * Map native PHP errors to priority
     *
     * @var array
     */
    public static $errorPriorityMap = array(
        E_NOTICE => self::NOTICE,
        E_USER_NOTICE => self::NOTICE,
        E_WARNING => self::WARN,
        E_CORE_WARNING => self::WARN,
        E_USER_WARNING => self::WARN,
        E_ERROR => self::ERR,
        E_USER_ERROR => self::ERR,
        E_CORE_ERROR => self::ERR,
        E_RECOVERABLE_ERROR => self::ERR,
        E_STRICT => self::DEBUG,
        E_DEPRECATED => self::DEBUG,
        E_USER_DEPRECATED => self::DEBUG,
    );
    /**
     * List of priority code => priority (short) name
     *
     * @var array
     */
    protected $priorities = array(
        self::EMERG => 'EMERG',
        self::ALERT => 'ALERT',
        self::CRIT => 'CRIT',
        self::ERR => 'ERR',
        self::WARN => 'WARN',
        self::NOTICE => 'NOTICE',
        self::INFO => 'INFO',
        self::DEBUG => 'DEBUG',
    );
    /**
     * @var \Logger\Writer\WriterAbstract
     */
    protected $writer = null;
    /**
     * @var boolean
     */
    protected $showBacktrace = false;
    /**
     * @var int
     */
    protected $logLevel = self::DEBUG;

    /**
     * @param WriterAbstract $writer
     */
    public function __construct(WriterAbstract $writer)
    {
        $this->writer = $writer;
    }

    public function __destruct()
    {
        $this->writer->shutdown();
    }

    /**
     * @param null|boolean $state
     *
     * @return \Logger\Logger|boolean
     *
     * @throws \Exception
     */
    public function showBacktrace($state = null)
    {
        if (!is_null($state)) {
             if (!is_bool($state)) {
                 throw new \Exception('State must be boolean.');
             }
            $this->showBacktrace = $state;
            return $this;
        } else {
            return $this->showBacktrace;
        }
    }

    /**
     * @param int $levelNumber
     * @param string $name
     *
     * @return \Logger\Logger
     *
     * @throws \Exception
     */
    public function addPriority(int $levelNumber, string $name)
    {
        if ($levelNumber <= self::DEBUG) {
            throw new \Exception('Additional log level must be higher than ' . self::DEBUG . '.');
        }
        $this->priorities[$levelNumber] = $name;
        return $this;
    }

    /**
     * @param mixed $msg
     * @param int $prio
     * @param null|mixed $extras
     * @param int $time
     * @param string $backtrace
     *
     * @return \Logger\Logger
     *
     * @throws \Exception
     */
    public function log($msg, $prio = self::INFO, $extras = null, int $time = null, $backtrace = null)
    {
        if (!is_int($prio) || !array_key_exists($prio, $this->priorities)) {
            throw new \Exception(' Priority level ' . $prio . ' not found.');
        }
        $time = is_null($time) ? time() : $time;
        if (!is_null($extras)) {
            $extras = var_export($extras, true);
        }
        if ($msg instanceof \Exception) {
           $msg = $msg->__toString();
        }
        if (!is_string($msg)) {
            $msg = var_export($msg, true);
        }
        if (is_null($backtrace) && $this->showBacktrace() == true) {
            ob_start();
            debug_print_backtrace();
            $backtrace  = ob_get_contents();
            ob_end_clean();
        } elseif (!is_null($backtrace)) {
            $backtrace = print_r($backtrace, true);
        }
        if ($this->logLevel >= $prio) {
            $this->writer->write($msg, $prio, $this->priorities[$prio], $extras, $time, $backtrace);
        }
        return $this;
    }
}