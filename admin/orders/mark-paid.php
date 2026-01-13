<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

mysqli_query($conn,"
    UPDATE orders
    SET billing_status='paid'
    WHERE id=$id
");

header("Location: view.php?id=".$id);
exit;
