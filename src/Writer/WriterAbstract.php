<?php
/**
 * 
 * @author dknx01 <e.witthauer@gmail.com>
 * @since 10.08.13 15:55
 * @package
 * 
 */

namespace Logger\Writer;


abstract class WriterAbstract
{
    /**
     * @param string $msg
     * @param int $prio
     * @param string $prioName
     * @param null|string $extras
     * @param int $time
     * @param null|string $backtrace
     *
     * @return mixed
     */
    abstract function write($msg, $prio, $prioName, $extras = null, $time, $backtrace = null);
    abstract function shutdown();
}