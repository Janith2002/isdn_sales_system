<?php
require_once "../app/config/db.php";
require_once "../app/helpers/auth.php";

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    mysqli_query($conn,"
        UPDATE orders SET status='$status' WHERE id=$id
    ");
    header("Location: orders.php");
    exit;
}

$order = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM orders WHERE id=$id")
);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Status | ISDN</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

<div class="page small">

<h2>Update Order #<?= $order['id'] ?></h2>

<div class="card">

<p><strong>Customer:</strong> <?= $order['customer_name'] ?></p>
<p><strong>Address:</strong> <?= $order['address'] ?></p>

<form method="post">
    <label>Status</label>
    <select name="status">
        <option value="processing" <?= $order['status']=='processing'?'selected':'' ?>>Processing</option>
        <option value="on_route" <?= $order['status']=='on_route'?'selected':'' ?>>On Route</option>
        <option value="delivered" <?= $order['status']=='delivered'?'selected':'' ?>>Delivered</option>
    </select>

    <button>Update</button>
</form>

</div>

</div>
</body>
</html>
