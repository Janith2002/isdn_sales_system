<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);

/* Order */
$oQ = mysqli_query($conn,"
    SELECT o.*, u.name customer_name
    FROM orders o
    JOIN users u ON u.id=o.user_id
    WHERE o.id=$orderId
");
if(mysqli_num_rows($oQ)==0) die("Order not found");
$order = mysqli_fetch_assoc($oQ);

/* Drivers */
$drivers = mysqli_query($conn,"
    SELECT id,name,email FROM users
    WHERE role='driver' AND status='active'
");

/* Update */
if($_SERVER['REQUEST_METHOD']==='POST'){
    $driverId = (int)$_POST['driver_id'];
    mysqli_query($conn,"
        UPDATE orders
        SET driver_id=$driverId, status='assigned'
        WHERE id=$orderId
    ");
    header("Location: view.php?id=".$orderId);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Reassign Driver</title>
<link rel="stylesheet" href="../../assets/css/admin.css">
<style>
.card{background:#fff;border-radius:18px;padding:22px;box-shadow:0 10px 26px rgba(0,0,0,.1)}
.driver{padding:14px;border:2px solid #eee;border-radius:14px;margin-bottom:10px;cursor:pointer}
.driver.active{border-color:#ff7a2f;background:#fff3e6}
.btn{width:100%;padding:14px;border-radius:16px;background:#ff7a2f;color:#fff;border:none;font-weight:700}
</style>
</head>
<body>
<div class="layout">
<?php include "../layout/sidebar.php"; ?>
<main class="main-content">
<?php $pageTitle="Reassign Driver"; include "../layout/header.php"; ?>

<div class="card">
<h3>Order #<?= $order['id'] ?></h3>
<p>Customer: <?= htmlspecialchars($order['customer_name']) ?></p>

<form method="post">
<input type="hidden" name="driver_id" id="driver_id">

<?php while($d=mysqli_fetch_assoc($drivers)): ?>
<div class="driver" onclick="pick(<?= $d['id'] ?>,this)">
<strong><?= htmlspecialchars($d['name']) ?></strong><br>
<small><?= htmlspecialchars($d['email']) ?></small>
</div>
<?php endwhile; ?>

<button class="btn" id="btn" disabled>Reassign Driver</button>
</form>
</div>

</main>
</div>
<script>
function pick(id,el){
  document.getElementById('driver_id').value=id;
  document.getElementById('btn').disabled=false;
  document.querySelectorAll('.driver').forEach(d=>d.classList.remove('active'));
  el.classList.add('active');
}
</script>
</body>
</html>
