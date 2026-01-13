<?php
session_start();
require_once "../../app/config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$orderId = (int)$data['order_id'];
$lat = (float)$data['lat'];
$lng = (float)$data['lng'];

mysqli_query($conn,"
    UPDATE orders
    SET driver_lat=$lat, driver_lng=$lng
    WHERE id=$orderId
");
