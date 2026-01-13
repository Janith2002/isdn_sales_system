<?php
session_start();
require_once "../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: /public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);
$userId  = (int)$_SESSION['user_id'];

$q = mysqli_query($conn, "
    SELECT 
        o.*,
        u.name AS driver_name,
        u.contact_number AS driver_contact
    FROM orders o
    LEFT JOIN users u ON u.id = o.driver_id
    WHERE o.id = $orderId AND o.user_id = $userId
");

if (!$q || mysqli_num_rows($q) === 0) {
    die('Order not found');
}

$order = mysqli_fetch_assoc($q);
$canTrack = in_array($order['status'], ['on_the_way','delivered']);

$lat = $order['driver_lat'] ?: 6.9271;
$lng = $order['driver_lng'] ?: 79.8612;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Track Order #<?= $orderId ?></title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.track-layout{
  display:grid;
  grid-template-columns:1fr;
  gap:24px;
}
.card-box{
  background:#fff;
  border-radius:22px;
  padding:24px;
  box-shadow:0 14px 34px rgba(0,0,0,.1);
}
.btn-group{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
  margin-top:18px;
}
.btn-solid{
  padding:12px 18px;
  background:#ff7a2f;
  color:#fff;
  border-radius:14px;
  font-weight:700;
  text-decoration:none;
}
.btn-outline{
  padding:12px 18px;
  border:2px solid #ff7a2f;
  color:#ff7a2f;
  border-radius:14px;
  font-weight:700;
  text-decoration:none;
}
#map{
  width:100%;
  height:260px;
  border-radius:16px;
}
</style>
</head>

<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
  <div class="logo">ISDN</div>
  <div class="nav-actions">
    <a href="orders.php" title="Back"><i class="fa fa-arrow-left"></i></a>
    <a href="home.php" title="Home"><i class="fa fa-home"></i></a>
    <a href="/public/logout.php" title="Logout"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout">
<main class="content">

<h2>Track Order #<?= $orderId ?></h2>

<div class="track-layout">

  <!-- MAP -->
  <div class="card-box">
    <h3>Live Driver Location</h3>

    <?php if ($canTrack): ?>
      <div id="map"></div>
    <?php else: ?>
      <p style="color:#6b7280">Driver has not started delivery yet</p>
    <?php endif; ?>
  </div>

  <!-- SUMMARY -->
  <div class="card-box">
    <h3>Order Summary</h3>

    <p><strong>Status:</strong> <?= ucfirst(str_replace('_',' ',$order['status'])) ?></p>
    <p><strong>Date:</strong> <?= date("d M Y, h:i A", strtotime($order['created_at'])) ?></p>
    <p><strong>Driver:</strong> <?= $order['driver_name'] ?: 'Not assigned' ?></p>
    <p><strong>Contact:</strong> <?= $order['driver_contact'] ?: '—' ?></p>

    <hr>

    <h2 style="color:#ff7a2f">
      LKR <?= number_format($order['total'],2) ?>
    </h2>

    <div class="btn-group">
      <a href="order-view.php?id=<?= $orderId ?>" class="btn-outline">
        View Order
      </a>
      <a href="orders.php" class="btn-solid">
        Back to Orders
      </a>
    </div>
  </div>

</div>

</main>
</div>

<!-- ✅ MOBILE BOTTOM NAV -->
<nav class="mobile-nav">
  <a href="/customer/orders.php">
    <i class="fa fa-arrow-left"></i>
    <span>Orders</span>
  </a>

  <a href="/customer/home.php">
    <i class="fa fa-home"></i>
    <span>Home</span>
  </a>

  <a href="/public/logout.php">
    <i class="fa fa-sign-out-alt"></i>
    <span>Logout</span>
  </a>
</nav>

<?php if ($canTrack): ?>
<script>
function initMap(){
  const pos = { lat: <?= $lat ?>, lng: <?= $lng ?> };
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 14,
    center: pos
  });
  new google.maps.Marker({
    position: pos,
    map: map,
    icon: "https://maps.google.com/mapfiles/ms/icons/orange-dot.png"
  });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhYVrJEQ3essDuWcVF9A5bm6uu_M7-Wd8&callback=initMap" async defer></script>
<?php endif; ?>

</body>
</html>
