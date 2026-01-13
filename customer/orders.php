<?php 
session_start();
require_once "../app/config/db.php";
require_once "../app/helpers/auth.php";

/* ROLE CHECK (SAFE) */
if ($_SESSION['role'] !== 'customer') {
    header("Location: /public/index.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];

$orders = mysqli_query($conn,"
    SELECT *
    FROM orders
    WHERE user_id = $userId
    ORDER BY id DESC
");

if (!$orders) {
    die(mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>My Orders</title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ================= ORDERS UI ================= */
.order-list{
  display:flex;
  flex-direction:column;
  gap:22px;
}
.order-card{
  background:#fff;
  border-radius:22px;
  box-shadow:0 14px 30px rgba(0,0,0,.08);
  padding:22px;
}
.order-head{
  display:flex;
  justify-content:space-between;
  align-items:center;
  flex-wrap:wrap;
  gap:10px;
}
.order-id{
  font-weight:800;
  font-size:18px;
}
.status{
  padding:6px 14px;
  border-radius:999px;
  font-weight:700;
  font-size:13px;
}
.pending{background:#fff3e6;color:#ff7a2f}
.assigned{background:#e6f0ff;color:#2563eb}
.on_the_way{background:#e0f2fe;color:#0284c7}
.delivered{background:#e6fffa;color:#059669}

.order-footer{
  margin-top:18px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  flex-wrap:wrap;
  gap:14px;
}
.order-total{
  font-size:22px;
  font-weight:800;
  color:#ff7a2f;
}

/* BUTTONS */
.btn-group{
  display:flex;
  gap:12px;
  flex-wrap:wrap;
}
a.btn-outline,
a.btn-solid{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:120px;
  padding:12px 18px;
  border-radius:14px;
  font-weight:700;
  text-decoration:none !important;
}
a.btn-outline{
  border:2px solid #ff7a2f;
  color:#ff7a2f;
}
a.btn-solid{
  background:#ff7a2f;
  color:#fff;
}

@media(max-width:768px){
  .order-footer{flex-direction:column;align-items:flex-start}
  .btn-group{width:100%}
  a.btn-outline,a.btn-solid{flex:1;width:100%}
}
</style>
</head>

<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
  <div class="logo">ISDN</div>
  <div class="nav-actions">
    <a href="home.php" title="Home"><i class="fa fa-home"></i></a>
    <a href="cart.php" title="Cart"><i class="fa fa-shopping-cart"></i></a>
    <a href="/public/logout.php" title="Logout"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout">
<main class="content">

<h2>My Orders</h2>

<?php if(mysqli_num_rows($orders) === 0): ?>
<div class="empty">
  <i class="fa fa-box"></i>
  <h3>No orders yet</h3>
</div>
<?php else: ?>

<div class="order-list">
<?php while($o = mysqli_fetch_assoc($orders)): ?>
<div class="order-card">

  <div class="order-head">
    <div class="order-id">Order #<?= $o['id'] ?></div>
    <span class="status <?= $o['status'] ?>">
      <?= ucfirst(str_replace('_',' ',$o['status'])) ?>
    </span>
  </div>

  <p style="margin-top:10px;color:#6b7280">
    <?= date("d M Y, h:i A", strtotime($o['created_at'])) ?>
  </p>

  <div class="order-footer">
    <div class="order-total">
      LKR <?= number_format($o['total'],2) ?>
    </div>

    <div class="btn-group">
      <a href="order-view.php?id=<?= $o['id'] ?>" class="btn-outline">View</a>
      <a href="track.php?id=<?= $o['id'] ?>" class="btn-solid">Track</a>
    </div>
  </div>

</div>
<?php endwhile; ?>
</div>

<?php endif; ?>

</main>
</div>

<!-- ✅ MOBILE BOTTOM NAV (NEW) -->
<nav class="mobile-nav">
  <a href="/customer/home.php">
    <i class="fa fa-home"></i>
    <span>Home</span>
  </a>

  <a href="/customer/cart.php">
    <i class="fa fa-shopping-cart"></i>
  </a>

  <a href="/customer/orders.php">
    <i class="fa fa-box"></i>
    <span>Orders</span>
  </a>

  <a href="/public/logout.php">
    <i class="fa fa-sign-out-alt"></i>
    <span>Logout</span>
  </a>
</nav>

</body>
</html>
