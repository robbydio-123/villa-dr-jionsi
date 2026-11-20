<?php

require_once __DIR__ . '/../app/admin_auth.php';
require_admin();

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(400);
    exit('Permintaan tidak valid.');
}

$stmt = db()->prepare('SELECT bukti_file FROM pembayaran WHERE id = :id');
$stmt->execute(['id' => $id]);
$file = $stmt->fetchColumn();

$root = realpath(dirname(__DIR__) . '/storage/bukti-pembayaran');
$path = ($file && $root) ? realpath(dirname(__DIR__) . '/' . ltrim((string)$file, '/')) : false;

if (!$path || strpos($path, $root . DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) {
    http_response_code(404);
    exit('Berkas bukti tidak ditemukan.');
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
    http_response_code(415);
    exit('Tipe berkas tidak didukung.');
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
readfile($path);