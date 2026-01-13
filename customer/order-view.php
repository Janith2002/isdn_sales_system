<?php
session_start();
require_once "../app/config/db.php";

/* ================= SECURITY ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);
$userId  = (int)$_SESSION['user_id'];

if ($orderId <= 0) {
    die("Invalid order ID");
}

/* ================= FETCH ORDER ================= */
$orderQ = mysqli_query($conn, "
    SELECT *
    FROM orders
    WHERE id = $orderId AND user_id = $userId
");

if (!$orderQ || mysqli_num_rows($orderQ) === 0) {
    die("Order not found");
}

$order = mysqli_fetch_assoc($orderQ);

/* ================= FETCH ITEMS ================= */
$itemsQ = mysqli_query($conn, "
    SELECT oi.qty, oi.price, p.name, p.image
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = $orderId
");

/* ================= INVOICE RULE ================= */
$canInvoice = ($order['billing_status'] === 'paid' || $order['status'] === 'delivered');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title>Order #<?= $order['id'] ?></title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.order-layout{display:grid;grid-template-columns:2fr 1fr;gap:28px}
.order-item{display:flex;gap:16px;align-items:center;background:#fff;padding:18px;border-radius:18px;box-shadow:0 10px 26px rgba(0,0,0,.08)}
.order-item img{width:90px;height:90px;border-radius:14px;object-fit:cover}
.summary{background:#fff;border-radius:22px;padding:24px;box-shadow:0 14px 34px rgba(0,0,0,.1)}
.summary-row{display:flex;justify-content:space-between;margin-bottom:14px}
.summary-total{font-size:26px;font-weight:800;color:#ff7a2f}
.status{padding:8px 16px;border-radius:999px;font-weight:700;font-size:14px}
.pending{background:#fff3e6;color:#ff7a2f}
.assigned{background:#e6f0ff;color:#2563eb}
.on_the_way{background:#e0f2fe;color:#0284c7}
.delivered{background:#e6fffa;color:#059669}
.btn-group{display:flex;gap:12px;flex-wrap:wrap}
a.btn-solid,a.btn-outline{
  display:inline-flex;align-items:center;justify-content:center;
  padding:12px 20px;border-radius:14px;font-weight:700;
  text-decoration:none!important;min-width:160px
}
a.btn-solid{background:#ff7a2f;color:#fff!important}
a.btn-outline{border:2px solid #ff7a2f;color:#ff7a2f!important}
a.btn-solid:hover{background:#e96a1c}
a.btn-outline:hover{background:#ff7a2f;color:#fff!important}
@media(max-width:768px){
  .order-layout{grid-template-columns:1fr}
  .order-item{flex-direction:column;align-items:flex-start}
  .order-item img{width:100%;height:160px}
  .btn-group{flex-direction:column}
  a.btn-solid,a.btn-outline{width:100%}
}
</style>
</head>

<body>

<nav class="navbar">
  <div class="logo">ISDN</div>
  <div class="nav-actions">
    <a href="orders.php"><i class="fa fa-arrow-left"></i></a>
    <a href="home.php"><i class="fa fa-home"></i></a>
    <a href="../public/logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout">
<main class="content">

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
  <div>
    <h2>Order #<?= $order['id'] ?></h2>
    <p style="color:#6b7280">
      <?= date("d M Y, h:i A", strtotime($order['created_at'])) ?>
    </p>
  </div>
  <span class="status <?= $order['status'] ?>">
    <?= strtoupper(str_replace('_',' ',$order['status'])) ?>
  </span>
</div>

<div class="order-layout" style="margin-top:24px">

<!-- ITEMS -->
<div>
<h3>Ordered Items</h3>
<div style="display:flex;flex-direction:column;gap:16px;margin-top:16px">
<?php while($i = mysqli_fetch_assoc($itemsQ)): ?>
<?php
$img = (!empty($i['image']) && file_exists("../uploads/products/".$i['image']))
    ? "../uploads/products/".$i['image']
    : "../uploads/products/default.png";
?>
<div class="order-item">
  <img src="<?= $img ?>">
  <div style="flex:1">
    <strong><?= htmlspecialchars($i['name']) ?></strong><br>
    Qty <?= $i['qty'] ?> × LKR <?= number_format($i['price'],2) ?>
  </div>
  <div style="font-weight:800;color:#ff7a2f">
    LKR <?= number_format($i['price'] * $i['qty'],2) ?>
  </div>
</div>
<?php endwhile; ?>
</div>
</div>

<!-- SUMMARY -->
<div class="summary">
<h3>Order Summary</h3>

<div class="summary-row"><span>Status</span><strong><?= ucfirst(str_replace('_',' ',$order['status'])) ?></strong></div>
<div class="summary-row"><span>Date</span><strong><?= date("d M Y", strtotime($order['created_at'])) ?></strong></div>

<hr style="margin:16px 0">

<div class="summary-total">LKR <?= number_format($order['total'],2) ?></div>

<div class="btn-group" style="margin-top:22px">

<?php if($canInvoice): ?>
<a href="invoice.php?id=<?= $orderId ?>" class="btn-solid">
  <i class="fa fa-file-invoice"></i>&nbsp; Invoice
</a>
<?php endif; ?>

<a href="track.php?id=<?= $orderId ?>" class="btn-outline">Track Order</a>
<a href="orders.php" class="btn-outline">Back to Orders</a>

</div>
</div>

</div>
</main>
</div>

</body>
</html>
