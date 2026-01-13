<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$where = "1";
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND o.id LIKE '%$s%'";
}
if ($status !== '') {
    $st = mysqli_real_escape_string($conn, $status);
    $where .= " AND o.status='$st'";
}

$q = mysqli_query($conn,"
    SELECT o.*, u.name AS driver_name
    FROM orders o
    LEFT JOIN users u ON u.id = o.driver_id
    WHERE $where
    ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Orders</title>

<link rel="stylesheet" href="../../assets/css/admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.filter-bar{
  display:flex;
  gap:14px;
  margin-bottom:22px;
}
.filter-bar input,
.filter-bar select{
  padding:10px 14px;
  border-radius:12px;
  border:1px solid #ddd;
}

.header-row,
.order-card{
  display:grid;
  grid-template-columns:1fr 1fr 1fr 1fr 1.3fr;
  align-items:center;
}

.header-row{
  font-weight:700;
  color:#6b7280;
  margin-bottom:10px;
}

.order-card{
  background:#fff;
  border-radius:18px;
  padding:18px 22px;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
  margin-bottom:14px;
}

.order-id{font-weight:800}

.status{
  padding:6px 14px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
  width:fit-content;
}
.pending{background:#fff3e6;color:#ff7a2f}
.assigned{background:#e6f0ff;color:#2563eb}
.on_the_way{background:#e0f2fe;color:#0284c7}
.delivered{background:#e6fffa;color:#059669}

.actions{
  display:flex;
  gap:10px;
}

/* BUTTON STYLE */
.action-btn{
  padding:8px 14px;
  border-radius:12px;
  font-size:13px;
  font-weight:600;
  text-decoration:none;
  border:2px solid #ff7a2f;
  color:#ff7a2f;
  background:#fff;
  transition:.2s;
}
.action-btn:hover{
  background:#ff7a2f;
  color:#fff;
}

/* PRIMARY */
.action-btn.primary{
  background:#ff7a2f;
  color:#fff;
}
</style>
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Orders"; include "../layout/header.php"; ?>

<!-- FILTER -->
<form class="filter-bar" method="get">
  <input type="text" name="search" placeholder="Search Order ID"
         value="<?= htmlspecialchars($search) ?>">
  <select name="status">
    <option value="">All Status</option>
    <option value="pending">Pending</option>
    <option value="assigned">Assigned</option>
    <option value="on_the_way">On the Way</option>
    <option value="delivered">Delivered</option>
  </select>
  <button class="action-btn primary">Filter</button>
</form>

<!-- HEADER -->
<div class="header-row">
  <div>Order</div>
  <div>Total</div>
  <div>Status</div>
  <div>Driver</div>
  <div>Actions</div>
</div>

<?php while($o = mysqli_fetch_assoc($q)): ?>
<div class="order-card">

  <div class="order-id">#<?= $o['id'] ?></div>

  <div>LKR <?= number_format($o['total'],2) ?></div>

  <div>
    <span class="status <?= $o['status'] ?>">
      <?= strtoupper(str_replace('_',' ',$o['status'])) ?>
    </span>
  </div>

  <div>
    <?= $o['driver_name'] ?? '<span style="color:#9ca3af">Not assigned</span>' ?>
  </div>

  <div class="actions">

    <?php if(!$o['driver_id']): ?>
      <a href="assign.php?id=<?= $o['id'] ?>" class="action-btn">
        Assign
      </a>
    <?php endif; ?>

    <a href="view.php?id=<?= $o['id'] ?>" class="action-btn">
      View
    </a>

    <!-- ✅ INVOICE BUTTON -->
    <a href="../../customer/invoice.php?id=<?= $o['id'] ?>" 
       class="action-btn">
      Invoice
    </a>

    <a href="track.php?id=<?= $o['id'] ?>" class="action-btn primary">
      Track
    </a>

  </div>

</div>
<?php endwhile; ?>

</main>
</div>

</body>
</html>
