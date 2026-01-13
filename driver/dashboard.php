<?php
session_start();

/* ================= AUTH ================= */
require_once __DIR__ . '/../app/helpers/auth.php';

if ($_SESSION['role'] !== 'driver') {
    header("Location: /public/index.php");
    exit;
}

/* ================= DB ================= */
require_once __DIR__ . '/../app/config/db.php';

$driverId  = (int)$_SESSION['user_id'];
$ratePerKm = 80;

/* ================= ACTIVE ORDERS ================= */
$orders = mysqli_query($conn, "
    SELECT id,total,status,created_at,
           delivery_name,delivery_phone,delivery_address
    FROM orders
    WHERE driver_id = $driverId
    AND status IN ('assigned','on_the_way')
    ORDER BY id DESC
");

/* ================= EARNINGS ================= */
$earn = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total_orders,
        IFNULL(SUM(IFNULL(delivery_distance,0) * $ratePerKm),0) AS total_earning
    FROM orders
    WHERE driver_id = $driverId
    AND status = 'delivered'
"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<!-- ✅ MOBILE VIEWPORT (REQUIRED) -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title>Driver Dashboard</title>

<link rel="stylesheet" href="/assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.card{background:#fff;border-radius:22px;padding:26px;box-shadow:0 14px 34px rgba(0,0,0,.12);margin-bottom:26px}
.status{padding:6px 14px;border-radius:999px;font-weight:700;font-size:13px}
.assigned{background:#e6f0ff;color:#2563eb}
.on_the_way{background:#fff3e6;color:#ff7a2f}
.btn{padding:12px 22px;border-radius:16px;font-weight:700;border:none;cursor:pointer}
.solid{background:#ff7a2f;color:#fff}
.addr{background:#f9fafb;padding:16px;border-radius:16px;margin-top:16px}
.map{margin-top:16px;height:250px;border-radius:18px;overflow:hidden}
.top-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:22px;margin-bottom:30px}
.top-card{background:#fff;border-radius:22px;padding:24px;box-shadow:0 14px 34px rgba(0,0,0,.12)}
.top-card h3{margin:0;font-size:14px;color:#6b7280}
.top-card h2{margin-top:10px;color:#ff7a2f}
</style>
</head>

<body>

<!-- ===== DESKTOP NAVBAR ===== -->
<nav class="navbar">
  <div class="logo">ISDN Driver</div>
  <div class="nav-actions">
    <a href="/driver/dashboard.php"><i class="fa fa-home"></i></a>
    <a href="/driver/earning.php"><i class="fa fa-wallet"></i></a>
    <a href="/driver/history.php"><i class="fa fa-clock"></i></a>
    <a href="/public/logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout">
<main class="content">

<!-- ===== TOP SUMMARY ===== -->
<div class="top-grid">
  <div class="top-card">
    <h3>Total Deliveries</h3>
    <h2><?= (int)$earn['total_orders'] ?></h2>
  </div>

  <div class="top-card">
    <h3>Total Earnings</h3>
    <h2>LKR <?= number_format($earn['total_earning'],2) ?></h2>
  </div>

  <div class="top-card">
    <h3>Rate</h3>
    <h2><?= $ratePerKm ?> LKR / km</h2>
  </div>
</div>

<h2>Active Deliveries</h2>

<?php if(mysqli_num_rows($orders) === 0): ?>
<div class="card"><p>No active deliveries</p></div>
<?php endif; ?>

<?php while($o = mysqli_fetch_assoc($orders)): ?>
<div class="card">
  <h3>Order #<?= (int)$o['id'] ?></h3>

  <span class="status <?= htmlspecialchars($o['status']) ?>">
    <?= strtoupper(str_replace('_',' ',$o['status'])) ?>
  </span>

  <p><strong>Order Total:</strong> LKR <?= number_format($o['total'],2) ?></p>

  <div class="addr">
    <strong><?= htmlspecialchars($o['delivery_name']) ?></strong><br>
    <?= htmlspecialchars($o['delivery_phone']) ?><br>
    <?= htmlspecialchars($o['delivery_address']) ?>
  </div>

  <div class="map">
    <iframe width="100%" height="100%" frameborder="0"
      src="https://www.google.com/maps?q=<?= urlencode($o['delivery_address']) ?>&output=embed">
    </iframe>
  </div>

  <div style="margin-top:16px">
    <?php if($o['status'] === 'assigned'): ?>
      <button class="btn solid" onclick="startDelivery(<?= (int)$o['id'] ?>)">
        Start Delivery
      </button>
    <?php else: ?>
      <a class="btn solid"
         href="/driver/update-status.php?id=<?= (int)$o['id'] ?>&status=delivered">
        Mark Delivered
      </a>
    <?php endif; ?>
  </div>
</div>
<?php endwhile; ?>

</main>
</div>

<!-- ✅ MOBILE BOTTOM NAV (DRIVER ONLY) -->
<nav class="mobile-nav">
  <a href="/driver/dashboard.php">
    <i class="fa fa-home"></i>
    <span>Home</span>
  </a>

  <a href="/driver/history.php">
    <i class="fa fa-clock"></i>
    <span>History</span>
  </a>

  <a href="/driver/earning.php">
    <i class="fa fa-wallet"></i>
    <span>Earnings</span>
  </a>

  <a href="/public/logout.php">
    <i class="fa fa-sign-out-alt"></i>
    <span>Logout</span>
  </a>
</nav>

<script>
function startDelivery(id){
    navigator.geolocation.getCurrentPosition(pos => {
        fetch("/driver/tracking/update-location.php", {
            method: "POST",
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({
                order_id: id,
                lat: pos.coords.latitude,
                lng: pos.coords.longitude
            })
        }).then(() => {
            location.href = "/driver/update-status.php?id=" + id + "&status=on_the_way";
        });
    });
}
</script>

</body>
</html>
