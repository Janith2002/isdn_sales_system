<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

/* ================= FILTER LOGIC ================= */
$where = "1";

$search   = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status   = $_GET['status'] ?? '';
$sort     = $_GET['sort'] ?? '';

if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (name LIKE '%$s%' OR sku LIKE '%$s%')";
}

if ($category !== '') {
    $c = mysqli_real_escape_string($conn, $category);
    $where .= " AND category='$c'";
}

if ($status !== '') {
    $st = mysqli_real_escape_string($conn, $status);
    $where .= " AND status='$st'";
}

$orderBy = "created_at DESC";
if ($sort === 'price_asc')  $orderBy = "price ASC";
if ($sort === 'price_desc') $orderBy = "price DESC";

/* ================= FETCH PRODUCTS ================= */
$result = mysqli_query(
    $conn,
    "SELECT * FROM products WHERE $where ORDER BY $orderBy"
);
if (!$result) {
    die("DB Error: " . mysqli_error($conn));
}

/* ================= FETCH CATEGORIES ================= */
$cats = mysqli_query($conn, "SELECT name FROM categories ORDER BY name");

/* ================= LOW STOCK COUNT ================= */
$lowStockCount = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total 
         FROM products 
         WHERE quantity <= min_quantity"
    )
)['total'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Products</title>
<link rel="stylesheet" href="../../assets/css/admin.css">

<style>
/* ===== FILTER + UI COLOR FIX ===== */
.filter-bar input,
.filter-bar select{
  border:2px solid #e5e7eb;
  border-radius:12px;
  padding:10px 14px;
}
.filter-bar input:focus,
.filter-bar select:focus{
  outline:none;
  border-color:#ff7a2f;
  box-shadow:0 0 0 3px rgba(255,122,47,.18);
}
.filter-bar option:checked,
.filter-bar option:hover{
  background:#ff7a2f;
  color:#fff;
}

/* ===== PAGE HEADER ===== */
.page-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
.page-actions{
  display:flex;
  gap:12px;
}

/* ===== ALERT ===== */
.alert-banner{
  background:#fff3e6;
  border-left:6px solid #ff7a2f;
  padding:14px 18px;
  border-radius:12px;
  margin-bottom:18px;
  font-weight:600;
}

/* ===== PRODUCT GRID ===== */
.product-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
  gap:26px;
}
.product-card{
  background:#fff;
  border-radius:18px;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
  overflow:hidden;
}
.product-card img{
  width:100%;
  height:180px;
  object-fit:cover;
}
.product-body{
  padding:18px;
}
.product-body h4{margin:0}
.category{color:#6b7280;font-size:13px}

/* ===== STOCK CONTROL ===== */
.stock-control{
  display:flex;
  align-items:center;
  gap:10px;
  margin:10px 0;
}
.stock-btn{
  width:30px;height:30px;
  border-radius:8px;
  border:none;
  background:#ffe8d6;
  color:#ff7a2f;
  font-weight:800;
  cursor:pointer;
}
.stock-btn:hover{
  background:#ff7a2f;
  color:#fff;
}
.danger{
  color:#dc2626;
  font-weight:700;
}

/* ===== BADGES ===== */
.badge{
  display:inline-block;
  padding:4px 12px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
}
.badge.green{background:#e6fffa;color:#059669}
.badge.red{background:#fee2e2;color:#b91c1c}

/* ===== ACTIONS ===== */
.card-actions{
  display:flex;
  justify-content:space-between;
  margin-top:12px;
}
.card-actions a{
  color:#ff7a2f;
  font-weight:600;
  text-decoration:none;
}
.card-actions a:hover{
  text-decoration:underline;
}
</style>
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Products"; include "../layout/header.php"; ?>

<!-- HEADER -->
<div class="page-header">
  <h2>Product Management</h2>
  <div class="page-actions">
    <a href="../categories/index.php" class="btn primary">+ Categories</a>
    <a href="create.php" class="btn primary">+ Add Product</a>
  </div>
</div>

<!-- LOW STOCK ALERT -->
<?php if ($lowStockCount > 0): ?>
<div class="alert-banner">
  ⚠ <?= $lowStockCount ?> product(s) are below minimum stock level (≤ 50)
</div>
<?php endif; ?>

<!-- FILTER BAR -->
<form class="filter-bar" method="get">

  <input type="text"
         name="search"
         placeholder="Search by name or SKU"
         value="<?= htmlspecialchars($search) ?>">

  <select name="category">
    <option value="">All Categories</option>
    <?php while ($cat = mysqli_fetch_assoc($cats)): ?>
      <option value="<?= htmlspecialchars($cat['name']) ?>"
        <?= $category==$cat['name']?'selected':'' ?>>
        <?= htmlspecialchars($cat['name']) ?>
      </option>
    <?php endwhile; ?>
  </select>

  <select name="status">
    <option value="">All Status</option>
    <option value="active" <?= $status=='active'?'selected':'' ?>>Active</option>
    <option value="inactive" <?= $status=='inactive'?'selected':'' ?>>Inactive</option>
  </select>

  <select name="sort">
    <option value="">Sort By</option>
    <option value="price_asc" <?= $sort=='price_asc'?'selected':'' ?>>
      Price: Low → High
    </option>
    <option value="price_desc" <?= $sort=='price_desc'?'selected':'' ?>>
      Price: High → Low
    </option>
  </select>

  <button class="btn primary">Filter</button>

</form>

<!-- PRODUCTS -->
<?php if (mysqli_num_rows($result) === 0): ?>
  <p>No products found.</p>
<?php else: ?>

<div class="product-grid">

<?php while ($p = mysqli_fetch_assoc($result)): ?>
<div class="product-card">

  <img src="../../uploads/products/<?= $p['image'] ?: 'default.png' ?>" alt="Product">

  <div class="product-body">
    <h4><?= htmlspecialchars($p['name']) ?></h4>
    <p class="category"><?= htmlspecialchars($p['category']) ?></p>

    <div class="stock-control">
      <form method="post" action="update-stock.php">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <input type="hidden" name="action" value="minus">
        <button class="stock-btn">−</button>
      </form>

      <span class="<?= $p['quantity'] <= $p['min_quantity'] ? 'danger' : '' ?>">
        <?= (int)$p['quantity'] ?>
      </span>

      <form method="post" action="update-stock.php">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <input type="hidden" name="action" value="plus">
        <button class="stock-btn">+</button>
      </form>
    </div>

    <span class="badge <?= $p['status']=='active'?'green':'red' ?>">
      <?= ucfirst($p['status']) ?>
    </span>

    <div class="card-actions">
      <a href="edit.php?id=<?= $p['id'] ?>">Edit</a>
      <a href="delete.php?id=<?= $p['id'] ?>"
         onclick="return confirm('Delete this product?')">
         Delete
      </a>
    </div>
  </div>

</div>
<?php endwhile; ?>

</div>
<?php endif; ?>

</main>
</div>

</body>
</html>
