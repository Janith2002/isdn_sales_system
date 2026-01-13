<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);

$q = mysqli_query($conn,"
    SELECT o.*,
           u.name AS driver_name,
           u.email AS driver_email
    FROM orders o
    LEFT JOIN users u ON u.id = o.driver_id
    WHERE o.id = $orderId
");

if (mysqli_num_rows($q) === 0) {
    die("Order not found");
}

$order = mysqli_fetch_assoc($q);

$canTrack = in_array($order['status'], ['on_the_way','delivered']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin – Track Order #<?= $orderId ?></title>

<link rel="stylesheet" href="../../assets/css/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.map-box{
  background:#fff;
  border-radius:18px;
  overflow:hidden;
  box-shadow:0 10px 28px rgba(0,0,0,.12);
}
.map-header{
  padding:16px 20px;
  font-weight:700;
}
#map{
  width:100%;
  height:420px;
}
.map-wait{
  height:420px;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#6b7280;
  font-weight:600;
  background:#f9fafb;
}
.info-box{
  background:#fff;
  border-radius:18px;
  padding:20px;
  box-shadow:0 10px 28px rgba(0,0,0,.12);
  margin-bottom:20px;
}
.badge{
  padding:6px 14px;
  border-radius:999px;
  font-size:13px;
  font-weight:700;
}
.assigned{background:#e6f0ff;color:#2563eb}
.on_the_way{background:#fff3e6;color:#ff7a2f}
.delivered{background:#e6fffa;color:#059669}
</style>
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Order Tracking"; include "../layout/header.php"; ?>

<div class="info-box">
  <h3>Order #<?= $order['id'] ?></h3>

  <p><strong>Status:</strong>
    <span class="badge <?= $order['status'] ?>">
      <?= strtoupper(str_replace('_',' ',$order['status'])) ?>
    </span>
  </p>

  <p><strong>Total:</strong> LKR <?= number_format($order['total'],2) ?></p>

  <p><strong>Driver:</strong>
    <?= $order['driver_name'] ?? 'Not Assigned' ?>
    <?php if($order['driver_email']): ?>
      (<?= $order['driver_email'] ?>)
    <?php endif; ?>
  </p>
</div>

<div class="map-box">
  <div class="map-header">Live Driver Location</div>

  <?php if($canTrack): ?>
    <div id="map"></div>
  <?php else: ?>
    <div class="map-wait">
      Driver has not started delivery yet
    </div>
  <?php endif; ?>
</div>

</main>
</div>

<?php if($canTrack): ?>
<script>
let map, marker;
let pos = { lat: 6.9271, lng: 79.8612 };

function initMap(){
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 13,
    center: pos
  });

  marker = new google.maps.Marker({
    position: pos,
    map: map,
    icon:"https://maps.google.com/mapfiles/ms/icons/orange-dot.png",
    title:"Driver"
  });

  setInterval(()=>{
    pos.lat += (Math.random()-0.5)*0.001;
    pos.lng += (Math.random()-0.5)*0.001;
    marker.setPosition(pos);
    map.panTo(pos);
  },3000);
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhYVrJEQ3essDuWcVF9A5bm6uu_M7-Wd8&callback=initMap" async defer></script>
<?php endif; ?>

</body>
</html>
