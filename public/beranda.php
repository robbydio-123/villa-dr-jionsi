<?php
require_once __DIR__ . '/../app/public_helpers.php';
expire_keeps(db());
$error = take_public_error();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/views/layouts/header.php';
require_once __DIR__ . '/../app/views/home/index.php';
require_once __DIR__ . '/../app/views/layouts/footer.php';
?>