<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

/* MUST BE POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = $_POST['action'] ?? '';

if ($id <= 0 || !in_array($action, ['plus','minus'])) {
    header("Location: index.php");
    exit;
}

/* UPDATE QUANTITY */
if ($action === 'plus') {
    mysqli_query($conn,"
        UPDATE products
        SET quantity = quantity + 1
        WHERE id = $id
    ");
}

if ($action === 'minus') {
    mysqli_query($conn,"
        UPDATE products
        SET quantity = IF(quantity > 0, quantity - 1, 0)
        WHERE id = $id
    ");
}

/* GO BACK */
header("Location: index.php");
exit;
