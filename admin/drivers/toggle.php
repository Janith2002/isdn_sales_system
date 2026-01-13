<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$id = (int)$_GET['id'];

$q = mysqli_query($conn,"SELECT status FROM users WHERE id=$id AND role='driver'");
$d = mysqli_fetch_assoc($q);

$newStatus = $d['status']=='active' ? 'inactive' : 'active';

mysqli_query($conn,"UPDATE users SET status='$newStatus' WHERE id=$id");

header("Location: index.php");
exit;
