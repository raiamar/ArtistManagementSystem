<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__.'/../../src/models/auth.php';
$page = $_GET['page'] ?? 'dashboard';

if ($page === 'artist' && isset($_GET['export']) && $_GET['export'] === 'csv' && hasRole('artist_manager')) {
    require_once __DIR__ . '/../../src/models/artist.php';
    ArtistHandler::exportCsv();
    exit;
}

$content = __DIR__ . "/pages/{$page}.php";

if(!file_exists($content))
{
    http_response_code(404);
    $content = __DIR__.'/pages/404.php';
}

require_once __DIR__.'/includes/layout.php';

requireAuth();