<?php
ob_start();

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'artist_management_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');


define('APP_NAME', 'Artist Management System');
define('APP_URL', 'http://localhost/record-manager/public/');
define('LOG_FILE', __DIR__ . '/logs/error.log');

if (!is_dir(dirname(LOG_FILE)))
    mkdir(dirname(LOG_FILE), 0775, true);

session_start();

function handleException(Throwable $e): void
{
    $message = "[" . date('Y-m-d H:i:s') . "] Uncaught Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
    error_log($message, 3, LOG_FILE);

    while (ob_get_level()) {
        ob_end_clean();
    }

    http_response_code(500);
    require __DIR__ . '/../public/500.php';
    exit;
}

function handleError(int $severity, string $message, string $file, int $line): bool
{
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

set_exception_handler('handleException');
set_error_handler('handleError');