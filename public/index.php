<?php
use Core\PHXRouter;
use Core\PHXLogger;
// use Core\PHXDatabase;

session_start();

require '../vendor/autoload.php';
require '../routes/web.php';
require '../routes/api.php';


set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    PHXLogger::displayError("Error: {$message} in {$file} on line {$line}");
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($exception) {
    PHXLogger::displayError("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        PHXLogger::displayError("Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}");
    }
});

// PHXDatabase::getInstance();
PHXRouter::Route();
