<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);

/* ================= FETCH ORDER + ADDRESS ================= */
$q = mysqli_query($conn,"
    SELECT o.*,
           cu.name AS customer_name,
           cu.email AS customer_email,
           dr.name AS driver_name,
           dr.email AS driver_email
    FROM orders o
    LEFT JOIN users cu ON cu.id = o.user_id
    LEFT JOIN users dr ON dr.id = o.driver_id
    WHERE o.id = $orderId
");

if (!$q || mysqli_num_rows($q) === 0) {
    die("Order not found");
}

$order = mysqli_fetch_assoc($q);

/* ================= UPDATE STATUS ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_status'])) {
    $allowed = ['pending','assigned','on_the_way','delivered'];
    $newStatus = $_POST['new_status'];

    if (in_array($newStatus, $allowed)) {
        mysqli_query($conn,"
            UPDATE orders
            SET status='$newStatus'
            WHERE id=$orderId
        ");
        header("Location: view.php?id=".$orderId);
        exit;
    }
}

/* ================= MARK PAID ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    mysqli_query($conn,"
        UPDATE orders
        SET billing_status='paid'
        WHERE id=$orderId
    ");
    header("Location: view.php?id=".$orderId);
    exit;
}

/* ================= ORDER ITEMS ================= */
$items = mysqli_query($conn,"
    SELECT p.name, p.image, oi.qty, oi.price
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = $orderId
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Order #<?= $orderId ?></title>

<link rel="stylesheet" href="../../assets/css/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.view-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:26px}
.card{background:#fff;border-radius:18px;padding:22px;box-shadow:0 10px 26px rgba(0,0,0,.08);margin-bottom:22px}
.status-pill{padding:8px 18px;border-radius:999px;font-size:13px;font-weight:700;cursor:pointer;border:2px solid transparent;background:#f3f4f6;color:#374151}
.status-pill.active{background:#ff7a2f;color:#fff;border-color:#ff7a2f}
.status-group{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
.item{display:flex;gap:16px;align-items:center;border-bottom:1px solid #eee;padding:14px 0}
.item:last-child{border:none}
.item img{width:60px;height:60px;border-radius:12px;object-fit:cover}
.actions{display:flex;gap:12px;flex-wrap:wrap}
.btn{padding:10px 18px;border-radius:14px;font-weight:600;text-decoration:none;background:#ff7a2f;color:#fff;border:none}
.btn.secondary{background:#fff;color:#ff7a2f;border:2px solid #ff7a2f}
.badge{padding:6px 14px;border-radius:999px;font-weight:700}
.paid{background:#e6fffa;color:#059669}
.unpaid{background:#fff3e6;color:#ff7a2f}
</style>
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>
<main class="main-content">
<?php $pageTitle="Order Details"; include "../layout/header.php"; ?>

<div class="view-grid">

<!-- LEFT -->
<div>

<div class="card">
<h3>Order Summary</h3>

<p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
<p><strong>Date:</strong> <?= date("d M Y", strtotime($order['created_at'])) ?></p>
<p><strong>Time:</strong> <?= date("H:i", strtotime($order['created_at'])) ?></p>

<h2 style="color:#ff7a2f;margin-top:14px">
LKR <?= number_format($order['total'],2) ?>
</h2>

<p>
<strong>Billing:</strong>
<span class="badge <?= $order['billing_status'] ?>">
<?= strtoupper($order['billing_status']) ?>
</span>
</p>

<?php if($order['billing_status']==='unpaid'): ?>
<form method="post">
<button name="mark_paid" class="btn" style="margin-top:10px">
Mark as Paid
</button>
</form>
<?php endif; ?>

<form method="post">
<input type="hidden" name="new_status" id="new_status">

<h4 style="margin-top:22px">Order Status</h4>
<div class="status-group">
<?php foreach(['pending','assigned','on_the_way','delivered'] as $s): ?>
<div class="status-pill <?= $order['status']===$s?'active':'' ?>"
onclick="setStatus('<?= $s ?>')">
<?= strtoupper(str_replace('_',' ',$s)) ?>
</div>
<?php endforeach; ?>
</div>
</form>
</div>

<div class="card">
<h3>Order Items</h3>

<?php while($i=mysqli_fetch_assoc($items)): ?>
<?php
$img = (!empty($i['image']) && file_exists("../../uploads/products/".$i['image']))
? "../../uploads/products/".$i['image']
: "../../assets/images/no-image.png";
?>
<div class="item">
<img src="<?= $img ?>">
<div style="flex:1">
<strong><?= htmlspecialchars($i['name']) ?></strong><br>
Qty: <?= $i['qty'] ?>
</div>
<strong>LKR <?= number_format($i['price']*$i['qty'],2) ?></strong>
</div>
<?php endwhile; ?>
</div>

</div>

<!-- RIGHT -->
<div>

<div class="card">
<h3>Customer</h3>
<p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
</div>

<div class="card">
<h3>Delivery Address</h3>
<p><strong>Name:</strong> <?= htmlspecialchars($order['delivery_name']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($order['delivery_phone']) ?></p>
<p><strong>Address:</strong><br><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></p>
</div>

<div class="card">
<h3>Driver</h3>
<?php if($order['driver_name']): ?>
<p><strong>Name:</strong> <?= htmlspecialchars($order['driver_name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($order['driver_email']) ?></p>
<?php else: ?>
<p style="color:#9ca3af">No driver assigned</p>
<?php endif; ?>
</div>

<div class="card">
<h3>Actions</h3>
<div class="actions">
<a href="index.php" class="btn secondary">Back</a>
<?php if(!$order['driver_id']): ?>
<a href="assign.php?id=<?= $orderId ?>" class="btn secondary">Assign Driver</a>
<?php endif; ?>
<a href="track.php?id=<?= $orderId ?>" class="btn">Track Order</a>
</div>
</div>

</div>

</div>

</main>
</div>

<script>
function setStatus(status){
document.getElementById('new_status').value = status;
document.forms[1].submit();
}
</script>

</body>
</html>
