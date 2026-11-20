<?php

require_once __DIR__ . '/../app/admin_dashboard.php';
require_admin();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

try {
    echo json_encode(dash_notification_data(db()), JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    error_log('[admin_notifikasi] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Notifikasi tidak dapat dimuat.']);
}