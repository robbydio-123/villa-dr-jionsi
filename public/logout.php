<?php
require_once __DIR__ . '/../app/admin_auth.php';
$_SESSION = [];
session_destroy();
redirect_to('login.php');