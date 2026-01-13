<?php
session_start();
require_once "../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    die("Unauthorized");
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status  = $_GET['status'] ?? '';

if ($orderId <= 0) {
    die("Invalid order");
}

if (!in_array($status, ['on_the_way','delivered'])) {
    die("Invalid status");
}

/* 🔥 FORCE UPDATE — NO driver_id BLOCK */
if ($status === 'delivered') {

    mysqli_query($conn,"
        UPDATE orders
        SET 
            status = 'delivered',
            delivered_at = NOW(),
            delivery_distance = IFNULL(delivery_distance, RAND(2,15))
        WHERE id = $orderId
    ");

} else {

    mysqli_query($conn,"
        UPDATE orders
        SET status = 'on_the_way'
        WHERE id = $orderId
    ");
}

/* 🔴 HARD REDIRECT BACK */
header("Location: orders/index.php");
exit;
