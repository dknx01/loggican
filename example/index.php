Logging Test
<hr>
<?php
/**
 * 
 * @author dknx01 <e.witthauer@gmail.com>
 * @since 10.08.13 20:41
 * @package
 * 
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once './test.php';

$path = realpath(__DIR__ . '/../log/output.log');
//var_dump($path);
$log = new \Logger\Logger(new \Logger\Writer\File($path));
$log->showBacktrace(true);
//var_dump($log );
//$log->log('TEST', \Logger\Logger::DEBUG, array('Foo' => 'bar'));
//echo '<hr>Output:<br>';
//echo nl2br(file_get_contents('../log/output.log'));
$pdo = new PDO('mysql:dbname=test;host=127.0.0.1', 'root', '');
$map = array('message' => \Logger\Writer\Db::LOGFIELD_MESSAGE,
             'time' => \Logger\Writer\Db::LOGFIELD_TIME,
             'additionals' => \Logger\Writer\Db::LOGFIELD_EXTRAS,
             'prio' => \Logger\Writer\Db::LOGFIELD_PRIORITYNAME,
             'backtrace' => \Logger\Writer\Db::LOGFIELD_BACKTRACE
            );
$dbWriter = new \Logger\Writer\Db($pdo, 'logging', $map);
$logger2 = new \Logger\Logger($dbWriter);
$logger2->log('EIN TEST');