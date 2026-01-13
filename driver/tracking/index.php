<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../../public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);
$driverId = $_SESSION['user_id'];

$q = mysqli_query($conn,"
    SELECT delivery_address, status
    FROM orders
    WHERE id=$orderId AND driver_id=$driverId
");

if(mysqli_num_rows($q) === 0){
    die("Order not found");
}

$o = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Live Tracking</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


<link rel="stylesheet" href="../../assets/css/customer-modern.css">

<style>
.map-box{
  height:420px;
  border-radius:18px;
  overflow:hidden;
}
</style>
</head>

<body>

<nav class="navbar">
  <div class="logo">Live Delivery</div>
  <div class="nav-actions">
    <a href="../dashboard.php">Back</a>
  </div>
</nav>

<div class="layout">
<main class="content">

<h2>Delivery Location</h2>

<?php if($o['status'] !== 'on_the_way'): ?>
<div class="empty">
  <h3>Start delivery to view map</h3>
</div>
<?php else: ?>

<div class="card">
  <div class="info">
    <strong>Delivery Address</strong><br>
    <?= htmlspecialchars($o['delivery_address']) ?>
  </div>
</div>

<div class="map-box">
<iframe
  width="100%"
  height="100%"
  frameborder="0"
  style="border:0"
  src="https://www.google.com/maps?q=<?= urlencode($o['delivery_address']) ?>&output=embed"
  allowfullscreen>
</iframe>
</div>

<?php endif; ?>

</main>
</div>

</body>
</html>
