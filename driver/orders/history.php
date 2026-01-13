<?php
session_start();
require_once "../../app/config/db.php";

/* SECURITY */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../../public/index.php");
    exit;
}

$driverId = (int)$_SESSION['user_id'];

$orders = mysqli_query($conn,"
    SELECT id, total, status, created_at
    FROM orders
    WHERE driver_id = $driverId
      AND status = 'delivered'
    ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


<title>Delivery History</title>

<link rel="stylesheet" href="../../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.history-card{
  background:#fff;
  border-radius:22px;
  padding:26px;
  box-shadow:0 14px 34px rgba(0,0,0,.12);
  display:grid;
  grid-template-columns:1fr auto;
  gap:22px;
  margin-bottom:22px;
}
.badge{
  padding:6px 14px;
  border-radius:999px;
  font-weight:700;
  background:#e6fffa;
  color:#059669;
}
</style>
</head>

<body>

<nav class="navbar">
  <div class="logo">ISDN <span style="font-weight:500">Driver</span></div>
  <div class="nav-actions">
    <a href="../dashboard.php"><i class="fa fa-arrow-left"></i></a>
    <a href="../../public/logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout">
<main class="content">

<h2>Delivery History</h2>

<?php if(mysqli_num_rows($orders) === 0): ?>
  <div class="empty">
    <h3>No completed deliveries yet</h3>
  </div>
<?php endif; ?>

<?php while($o = mysqli_fetch_assoc($orders)): ?>
<div class="history-card">

  <div>
    <h3>Order #<?= $o['id'] ?></h3>
    <p style="color:#6b7280">
      Delivered on <?= date("d M Y, h:i A", strtotime($o['created_at'])) ?>
    </p>
    <strong>LKR <?= number_format($o['total'],2) ?></strong>
  </div>

  <div class="badge">DELIVERED</div>

</div>
<?php endwhile; ?>

</main>
</div>

</body>
</html>
