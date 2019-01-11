<?php
//$h = fopen('/tmp/pool.txt', 'r');
//$h2 = $h;
//$h2 = null;
//
//xdebug_debug_zval('h');
//xdebug_debug_zval('h2');
//echo PHP_EOL;
//echo fgets($h, 10);
//echo PHP_EOL;
//die;
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "/vendor/autoload.php";
Dtsf\Dtsf::run();