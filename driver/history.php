<?php
session_start();
require_once "../app/config/db.php";

/* ================= SECURITY ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'driver') {
    header("Location: ../public/index.php");
    exit;
}

$driverId = (int)$_SESSION['user_id'];

/* ================= FETCH DELIVERY HISTORY ================= */
$orders = mysqli_query($conn,"
    SELECT id, total, created_at
    FROM orders
    WHERE driver_id = $driverId
      AND status = 'delivered'
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">



<title>Delivery History</title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===== HISTORY PAGE UI ===== */

.page-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:30px;
}

.history-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
  gap:26px;
}

.history-card{
  background:#fff;
  border-radius:22px;
  padding:26px;
  box-shadow:0 14px 34px rgba(0,0,0,.12);
  transition:.25s;
}
.history-card:hover{
  transform:translateY(-6px);
}

.history-card h3{
  margin:0 0 10px;
}

.meta{
  color:#6b7280;
  font-size:14px;
  margin-bottom:14px;
}

.status{
  display:inline-block;
  padding:6px 14px;
  border-radius:999px;
  font-size:13px;
  font-weight:700;
  background:#e6fffa;
  color:#059669;
}

.total{
  font-size:20px;
  font-weight:800;
  color:#ff7a2f;
  margin-top:14px;
}

.empty-box{
  background:#fff;
  padding:70px;
  border-radius:22px;
  text-align:center;
  box-shadow:0 14px 34px rgba(0,0,0,.12);
  color:#6b7280;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="logo">ISDN <span style="font-weight:500">Driver</span></div>
  <div class="nav-actions">
    <a href="dashboard.php" title="Back to Home">
      <i class="fa fa-home"></i>
    </a>
    <a href="../public/logout.php" title="Logout">
      <i class="fa fa-sign-out-alt"></i>
    </a>
  </div>
</nav>

<div class="layout">
<main class="content">

<!-- HEADER -->
<div class="page-header">
  <h2>Delivery History</h2>
</div>

<?php if (!$orders || mysqli_num_rows($orders) === 0): ?>

<div class="empty-box">
  <i class="fa fa-box-open" style="font-size:52px;color:#ff7a2f"></i>
  <h3>No completed deliveries</h3>
  <p>Your delivered orders will appear here.</p>
</div>

<?php else: ?>

<div class="history-grid">

<?php while($o = mysqli_fetch_assoc($orders)): ?>
<div class="history-card">

  <h3>Order #<?= $o['id'] ?></h3>

  <div class="meta">
    Delivered on <?= date("d M Y, h:i A", strtotime($o['created_at'])) ?>
  </div>

  <span class="status">DELIVERED</span>

  <div class="total">
    LKR <?= number_format($o['total'],2) ?>
  </div>

</div>
<?php endwhile; ?>

</div>
<?php endif; ?>

</main>
</div>

</body>
</html>
