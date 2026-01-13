<?php
session_start();
require_once "../app/config/db.php";

if ($_SESSION['role'] !== 'driver') {
    header("Location: ../public/index.php");
    exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
$status  = $_POST['status'] ?? '';

if ($orderId <= 0 || $status === '') {
    header("Location: dashboard.php");
    exit;
}

/* UPDATE ORDER STATUS */
mysqli_query($conn,"
    UPDATE orders
    SET status='$status'
    WHERE id=$orderId
      AND driver_id=".$_SESSION['user_id']."
");

/* GPS SIMULATION (SRI LANKA AREA) */
$lat = 6.9271 + (rand(-20,20) / 1000);
$lng = 79.8612 + (rand(-20,20) / 1000);

mysqli_query($conn,"
    INSERT INTO order_tracking (order_id, latitude, longitude)
    VALUES ($orderId, '$lat', '$lng')
");

header("Location: dashboard.php");
exit;
