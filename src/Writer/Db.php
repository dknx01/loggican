<?php
/**
 * 
 * @author dknx01 <e.witthauer@gmail.com>
 * @since 11.08.13 15:59
 * @package
 * 
 */

namespace Logger\Writer;

use \Logger\Writer\WriterAbstract;

class Db extends WriterAbstract
{
    /**
     * @var \PDO
     */
    protected $db = null;
    /**
     * @var array
     */
    protected $map = array();
    /**
     * @var string
     */
    protected $tableName = 'logging';
    /**
     * @param \Pdo $db
     */
    /**
     * @var string
     */
    protected $message = '';
    const LOGFIELD_MESSAGE = 'message';
    /**
     * @var int
     */
    protected $priotity = \Logger\Logger::INFO;
    const LOGFIELD_PRIORITY = 'priority';
    /**
     * @var string
     */
    protected $priorityName = '';
    const LOGFIELD_PRIORITYNAME = 'priorityName';
    /**
     * @var null|string
     */
    protected $extras = null;
    const LOGFIELD_EXTRAS = 'extras';
    /**
     * @var int
     */
    protected $time = 0;
    const LOGFIELD_TIME = 'time';
    /**
     * @var null|string
     */
    protected $backtrace = null;
    const LOGFIELD_BACKTRACE = 'backtrace';

    /**
     * @param \Pdo $db
     * @param string|null $tableName
     * @param array|null $map
     */
    public function __construct(\Pdo $db, $tableName = null, array $map = null)
    {
        $this->db = $db;
        if (is_null($map)) {
            $this->map = array('message' => self::LOGFIELD_MESSAGE,
                'time' => self::LOGFIELD_TIME,
                'additionals' => self::LOGFIELD_EXTRAS,
                'prio' => self::LOGFIELD_PRIORITYNAME,
                'backtrace' => self::LOGFIELD_BACKTRACE
            );
        } else {
            $this->map = $map;
        }
        $this->tableName = is_null($tableName) ? $this->tableName : $tableName;
        $this->priorityName = array_search($this->priotity, \Logger\Logger::$errorPriorityMap);
    }
    /**
     * @param string $msg
     * @param int $prio
     * @param string $prioName
     * @param null $extras
     * @param int $time
     * @param null $backtrace
     * @return mixed|void
     */
    public function write($message, $priority, $priorityName, $extras = null, $time, $backtrace = null)
    {
        $this->setMessage($message)
             ->setPriotity($priority)
             ->setPriorityName($priorityName)
             ->setExtras($extras)
             ->setTime($time)
             ->setBacktrace($backtrace);
        $sql = 'INSERT INTO `' . $this->tableName . '` SET ';
        foreach($this->map as $column => $param) {
            $getter = 'get' . ucfirst($param);
            $sql .= '`' . $column . '`=' . $this->db->quote($this->$getter()) . ',';
        }
        $sql = substr($sql, 0, -1);
        try {
            $this->db->query($sql);
        } catch (\PDOException $e)
        {
            var_dump($e->getMessage());
            throw new WriterException('There was an error while insert in log entry. ' . $e->getMessage());
        }
    }
    public function shutdown()
    {
    }

    /**
     * @param int $time
     *
     * @return \Logger\Writer\Db
     */
    protected function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @return int
     */
    protected function getTime()
    {
        return $this->time;
    }

    /**
     * @param int $priotity
     *
     * @return \Logger\Writer\Db
     */
    protected function setPriotity($priotity)
    {
        $this->priotity = $priotity;
        return $this;
    }

    /**
     * @return int
     */
    protected function getPriotity()
    {
        return $this->priotity;
    }

    /**
     * @param string $priorityName
     *
     * @return \Logger\Writer\Db
     */
    protected function setPriorityName($priorityName)
    {
        $this->priorityName = $priorityName;
        return $this;
    }

    /**
     * @return string
     */
    protected function getPriorityName()
    {
        return $this->priorityName;
    }

    /**
     * @param string $message
     *
     * @return \Logger\Writer\Db
     */
    protected function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    protected function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null|string $extras
     *
     * @return \Logger\Writer\Db
     */
    protected function setExtras($extras)
    {
        $this->extras = $extras;
        return $this;
    }

    /**
     * @return null|string
     */
    protected function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param null|string $backtrace
     *
     * @return \Logger\Writer\Db
     */
    protected function setBacktrace($backtrace)
    {
        $this->backtrace = $backtrace;
        return $this;
    }

    /**
     * @return null|string
     */
    protected function getBacktrace()
    {
        return $this->backtrace;
    }
}