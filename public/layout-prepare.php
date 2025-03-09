<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_error_handler(function (int $errNo, string $errStr, string $errFile, int $errLine) {
    throw new \ErrorException('ERROR : ' . $errStr, $errNo, 0, $errFile, $errLine);
});
set_exception_handler(function(Throwable $e) {
    ob_end_clean();
    echo "<pre>".$e->getTraceAsString()."</pre>";
});

require dirname(__DIR__) . '/vendor/autoload.php';

ob_start();
