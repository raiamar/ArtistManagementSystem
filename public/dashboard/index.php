<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/models/auth.php';
require_once __DIR__ . '/../../src/models/artist.php';

requireAuth();
$user = currenctUser();

$defaultPage = match ($user['role']) {
    'super_admin' => 'dashboard',
    'artist_manager' => 'artist',
    'artist' => 'song',
    default => 'dashboard',
};

$page = $_GET['page'] ?? $defaultPage;

$allowedPages = match ($user['role']) {
    'super_admin' => ['dashboard', 'user', 'artist', 'song'],
    'artist_manager' => ['artist', 'song'],
    'artist' => ['song'],
    default => [],
};

if (!in_array($page, $allowedPages)) {
    http_response_code(404);
    $content = __DIR__ . '/pages/404.php';
} else {

    if ($page === 'artist' && isset($_GET['export']) && $_GET['export'] === 'csv' && hasRole('artist_manager')) {
        require_once __DIR__ . '/../../src/models/artist.php';
        ArtistHandler::exportCsv();
        exit;
    }
    if (isset($_GET['sample']) && $_GET['sample'] === 'artist-csv') {
        ArtistHandler::exportSampleCsv();
        exit;
    }

    $content = __DIR__ . "/pages/{$page}.php";

    if (!file_exists($content)) {
        http_response_code(404);
        $content = __DIR__ . '/pages/404.php';
    }
}
require_once __DIR__ . '/includes/layout.php';
