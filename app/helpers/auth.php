<?php
// DO NOT start session here if already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['user_id']) || empty($_SESSION['role'])) {
    header("Location: /public/index.php");
    exit;
}
