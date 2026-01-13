<?php
session_start();
require_once "../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../public/index.php");
    exit;
}

$driverId  = (int)$_SESSION['user_id'];
$ratePerKm = 80;

$orders = mysqli_query($conn,"
    SELECT id, delivery_distance, created_at
    FROM orders
    WHERE driver_id=$driverId
    AND status='delivered'
    ORDER BY created_at DESC
");

$totalEarning = 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Driver Earnings</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.card{background:#fff;border-radius:22px;padding:26px;box-shadow:0 14px 34px rgba(0,0,0,.12);margin-bottom:26px}
.earning-row{display:flex;justify-content:space-between;padding:14px 0;border-bottom:1px solid #eee}
.total-box{background:#ff7a2f;color:#fff;border-radius:22px;padding:26px;text-align:center}
</style>
</head>

<body>

<nav class="navbar">
  <div class="logo">ISDN Driver</div>
  <div class="nav-actions">
    <a href="dashboard.php"><i class="fa fa-home"></i></a>
    <a href="../public/logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout"><main class="content">

<h2>Delivery Earnings</h2>

<?php while($o=mysqli_fetch_assoc($orders)):
$distance = (float)($o['delivery_distance'] ?? 0);
$earning  = $distance * $ratePerKm;
$totalEarning += $earning;
?>
<div class="card">
  <div class="earning-row">
    <strong>Order #<?= $o['id'] ?></strong>
    <strong>LKR <?= number_format($earning,2) ?></strong>
  </div>
  <small>Distance: <?= $distance ?> km × <?= $ratePerKm ?> LKR</small><br>
  <small>Date: <?= date("d M Y", strtotime($o['created_at'])) ?></small>
</div>
<?php endwhile; ?>

<div class="total-box">
  <h3>Total Earnings</h3>
  <h1>LKR <?= number_format($totalEarning,2) ?></h1>
</div>

</main></div>

</body>
</html>
