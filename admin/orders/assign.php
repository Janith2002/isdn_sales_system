<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);

/* Order info */
$oQ = mysqli_query($conn,"
    SELECT o.*, u.name AS customer_name, u.email AS customer_email
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE o.id = $orderId
");

if (mysqli_num_rows($oQ) === 0) {
    die("Order not found");
}
$order = mysqli_fetch_assoc($oQ);

/* Drivers */
$drivers = mysqli_query($conn,"
    SELECT id, name, email
    FROM users
    WHERE role='driver' AND status='active'
");

/* Assign */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driverId = (int)$_POST['driver_id'];

    mysqli_query($conn,"
        UPDATE orders
        SET driver_id = $driverId, status='assigned'
        WHERE id = $orderId
    ");

    header("Location: view.php?id=".$orderId);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Assign Driver</title>

<link rel="stylesheet" href="../../assets/css/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.assign-grid{
  display:grid;
  grid-template-columns:1.2fr 1fr;
  gap:28px;
}

.card{
  background:#fff;
  border-radius:18px;
  padding:22px;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
}

.driver-list{
  display:grid;
  gap:14px;
  margin-top:14px;
}

.driver-card{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:16px 18px;
  border-radius:16px;
  border:2px solid #e5e7eb;
  cursor:pointer;
  transition:.2s;
}
.driver-card:hover{
  border-color:#ff7a2f;
}
.driver-card.active{
  border-color:#ff7a2f;
  background:#fff3e6;
}

.driver-info strong{
  display:block;
}

.assign-btn{
  margin-top:22px;
  padding:12px 22px;
  border-radius:16px;
  background:#ff7a2f;
  color:#fff;
  font-weight:700;
  border:none;
  cursor:pointer;
  width:100%;
}
.assign-btn:disabled{
  background:#ddd;
  cursor:not-allowed;
}

.status-pill{
  padding:6px 14px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
  background:#fff3e6;
  color:#ff7a2f;
}
</style>
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Assign Driver"; include "../layout/header.php"; ?>

<div class="assign-grid">

<!-- LEFT -->
<div class="card">
  <h3>Order Summary</h3>

  <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
  <p><strong>Date:</strong> <?= date("d M Y", strtotime($order['created_at'])) ?></p>
  <p><strong>Time:</strong> <?= date("H:i", strtotime($order['created_at'])) ?></p>

  <p><strong>Status:</strong>
    <span class="status-pill">
      <?= strtoupper(str_replace('_',' ',$order['status'])) ?>
    </span>
  </p>

  <h2 style="color:#ff7a2f">
    LKR <?= number_format($order['total'],2) ?>
  </h2>

  <hr style="margin:18px 0">

  <h4>Customer</h4>
  <p><strong><?= htmlspecialchars($order['customer_name']) ?></strong></p>
  <p><?= htmlspecialchars($order['customer_email']) ?></p>
</div>

<!-- RIGHT -->
<div class="card">
  <h3>Select Driver</h3>

  <form method="post">
    <input type="hidden" name="driver_id" id="driver_id">

    <div class="driver-list">
      <?php while($d = mysqli_fetch_assoc($drivers)): ?>
      <div class="driver-card"
           onclick="selectDriver(<?= $d['id'] ?>, this)">
        <div class="driver-info">
          <strong><?= htmlspecialchars($d['name']) ?></strong>
          <small><?= htmlspecialchars($d['email']) ?></small>
        </div>
        <i class="fa fa-user"></i>
      </div>
      <?php endwhile; ?>
    </div>

    <button class="assign-btn" id="assignBtn" disabled>
      Assign Driver
    </button>
  </form>
</div>

</div>

</main>
</div>

<script>
function selectDriver(id, el){
  document.getElementById('driver_id').value = id;
  document.getElementById('assignBtn').disabled = false;

  document.querySelectorAll('.driver-card').forEach(c=>{
    c.classList.remove('active');
  });
  el.classList.add('active');
}
</script>

</body>
</html>
