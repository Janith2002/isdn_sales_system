<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role']!=='admin') exit;

$orderId=(int)$_GET['id'];

mysqli_query($conn,"
  UPDATE orders
  SET status='cancelled'
  WHERE id=$orderId
");

header("Location: view.php?id=".$orderId);
exit;
