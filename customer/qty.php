<?php
session_start();

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!isset($_SESSION['cart'][$id])) {
    header("Location: cart.php");
    exit;
}

if ($action === 'inc') {
    $_SESSION['cart'][$id]++;
}

if ($action === 'dec' && $_SESSION['cart'][$id] > 1) {
    $_SESSION['cart'][$id]--;
}

header("Location: cart.php");
exit;
