<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../../public/index.php");
    exit;
}

$driverId = (int)$_SESSION['user_id'];

/* SHOW ONLY ACTIVE ORDERS */
$orders = mysqli_query($conn,"
    SELECT *
    FROM orders
    WHERE status IN ('assigned','on_the_way')
    AND driver_id = $driverId
    ORDER BY id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Driver Orders</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


<link rel="stylesheet" href="../../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.card{
  background:#fff;
  border-radius:22px;
  padding:26px;
  box-shadow:0 14px 34px rgba(0,0,0,.12);
  margin-bottom:26px
}
.status{
  padding:6px 14px;
  border-radius:999px;
  font-weight:700;
  font-size:13px
}
.assigned{background:#e6f0ff;color:#2563eb}
.on_the_way{background:#fff3e6;color:#ff7a2f}
.btn{
  padding:14px 26px;
  border-radius:16px;
  font-weight:700;
  border:none;
  cursor:pointer
}
.solid{background:#ff7a2f;color:#fff}
.map{
  margin-top:16px;
  height:250px;
  border-radius:18px;
  overflow:hidden
}
</style>
</head>

<body>

<nav class="navbar">
  <div class="logo">ISDN Driver</div>
  <div class="nav-actions">
    <a href="../dashboard.php"><i class="fa fa-home"></i></a>
    <a href="../earning.php"><i class="fa fa-wallet"></i></a>
    <a href="../history.php"><i class="fa fa-clock"></i></a>
    <a href="../../public/logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout"><main class="content">

<h2>Active Orders</h2>

<?php if(mysqli_num_rows($orders) === 0): ?>
<div class="card">
  <p>No active orders</p>
</div>
<?php endif; ?>

<?php while($o = mysqli_fetch_assoc($orders)): ?>
<div class="card">

<h3>Order #<?= $o['id'] ?></h3>

<span class="status <?= $o['status'] ?>">
<?= strtoupper(str_replace('_',' ',$o['status'])) ?>
</span>

<p><strong>Total:</strong> LKR <?= number_format($o['total'],2) ?></p>

<!-- ✅ MAP ALWAYS VISIBLE UNTIL DELIVERED -->
<div class="map">
  <iframe
    width="100%"
    height="100%"
    frameborder="0"
    src="https://www.google.com/maps?q=<?= urlencode($o['delivery_address']) ?>&output=embed">
  </iframe>
</div>

<!-- ✅ BUTTON LOGIC -->
<div style="margin-top:20px">
<?php if($o['status'] === 'assigned'): ?>
  <a class="btn solid"
     href="../update-status.php?id=<?= $o['id'] ?>&status=on_the_way">
     Start Delivery
  </a>
<?php else: ?>
  <a class="btn solid"
     href="../update-status.php?id=<?= $o['id'] ?>&status=delivered">
     Mark Delivered
  </a>
<?php endif; ?>
</div>

</div>
<?php endwhile; ?>

</main></div>

</body>
</html>
