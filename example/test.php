<?php
function myLoader($classname)
{
    if (strpos($classname, '\\')) {
        $appdir = realpath(__DIR__ . '/../src');
        $classname = ltrim($classname, '\\');
        $classname = (substr($classname, 0, 6) == 'Logger')
                     ? ltrim(substr($classname, 6),'\\')
                     : $classname;
        $filename = $appdir . '/' . str_replace(array('\\', '_'), '/', $classname);
    } else {
        $filename = APPDIR . '/' . str_replace('_', '/', $classname);
    }
    if (file_exists($filename . '.php')) {
        require_once $filename . '.php';
    }
}

spl_autoload_extensions('.php'); // comma-separated list
spl_autoload_register('myLoader');