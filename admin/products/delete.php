<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid product ID");
}

/* FETCH IMAGE */
$q = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
$p = mysqli_fetch_assoc($q);

if ($p) {
    if ($p['image'] && file_exists("../../uploads/products/".$p['image'])) {
        unlink("../../uploads/products/".$p['image']);
    }

    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
}

header("Location: index.php");
exit;
