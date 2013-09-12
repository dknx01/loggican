<?php
/**
 * 
 * @author dknx01 <e.witthauer@gmail.com>
 * @since 10.08.13 21:36
 * @package
 * 
 */

namespace Logger\Writer;

use \Logger\Writer\WriterAbstract as WriterAbstract;
use \Logger\Writer\WriterException;

class File extends WriterAbstract
{
    /**
     * @var string
     */
    protected $fileWithPath = '';
    /**
     * @var null
     */
    protected $resource = null;
    /**
     * @var string
     */
    protected $mode = 'a';
    /**
     * @var string
     */
    protected $seperator = "\t";

    /**
     * @param string $logfile path to the log file
     */
    public function __construct($logfile, $mode = 'a')
    {;
        $this->fileWithPath = $logfile;
        $this->mode = $mode;
        $this->checkFile();
    }

    public function shutdown()
    {
        fclose($this->resource);
    }

    protected function checkFile()
    {
        if (in_array(substr($this->fileWithPath, 0, 2), array('./', '..'))) {
            throw new WriterException('Log file path cannot start with a relative path');
        }
        if (!file_exists($this->fileWithPath)) {
            $folder = substr($this->fileWithPath, 0, strrpos($this->fileWithPath, DIRECTORY_SEPARATOR) + 1);
            if (!file_exists($folder)) {
                if (mkdir($folder) == false) {
                    throw new WriterException('Can\'t create log folder ' . $folder);
                }
            }
        }
        $this->resource = fopen($this->fileWithPath, $this->mode);
    }

    /**
     * @param string $msg
     * @param int $prio
     * @param string $prioName
     * @param null|string $extras
     * @param int $time
     * @param null|string $backtrace
     */
    public function write($msg, $prio, $prioName, $extras = null, $time, $backtrace = null)
    {
        $prioName = str_replace('"', '\"', $prioName);
        $msg = str_replace('"', '\"', $msg);

        $line = date('Y-m-d H:i:sP', $time)
            . $this->seperator . '"' . $prioName . '"'
            . $this->seperator . '"' . $msg . '"';
        if (!is_null($extras)) {
            $line .= $this->seperator . '"' . str_replace('"', '\"', $extras) . '"';
        } else {
            $line .= $this->seperator . '-';
        }
        if (!is_null($backtrace)) {
            $line .= $this->seperator . '"' . str_replace('"', '\"', $backtrace) . '"';
        } else {
            $line .= $this->seperator . '-';
        }
        fwrite($this->resource, $line . PHP_EOL);
    }
}