<?php
require_once __DIR__ . '/../../config/config.php';
$page = $_GET['page'] ?? 'dashboard';

$content = __DIR__ . "/pages/{$page}.php";

if(!file_exists($content))
{
    http_response_code(404);
    $content = __DIR__.'/pages/404.php';
}

require_once __DIR__.'/includes/layout.php';