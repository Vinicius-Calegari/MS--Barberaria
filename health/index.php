<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    $pdo = getDBConnection();
    $pdo->query('SELECT 1');
    http_response_code(200);
    echo json_encode(['status' => 'ok'], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    error_log('Healthcheck falhou: ' . $e->getMessage());
    http_response_code(503);
    echo json_encode(['status' => 'unavailable'], JSON_UNESCAPED_SLASHES);
}
