<?php
session_start();
require_once "../app/config/db.php";

/* AUTH CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /public/login.php");
    exit;
}

/* FILTER INPUTS */
$search   = mysqli_real_escape_string($conn, $_GET['search'] ?? "");
$category = mysqli_real_escape_string($conn, $_GET['category'] ?? "");
$min      = (int)($_GET['min'] ?? 0);
$max      = (int)($_GET['max'] ?? 0);
$sort     = $_GET['sort'] ?? "";

/* LOAD CATEGORIES */
$categories = mysqli_query($conn,"SELECT name FROM categories ORDER BY name");

/* PRODUCT QUERY */
$sql = "SELECT * FROM products WHERE status='active'";
if ($search !== "") $sql .= " AND name LIKE '%$search%'";
if ($category !== "") $sql .= " AND category='$category'";
if ($min > 0) $sql .= " AND price >= $min";
if ($max > 0 && $max >= $min) $sql .= " AND price <= $max";
$sql .= ($sort === "low_high") ? " ORDER BY price ASC" :
        (($sort === "high_low") ? " ORDER BY price DESC" : " ORDER BY id DESC");

$products = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>ISDN Store</title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="logo">ISDN</div>

  <form class="search" method="get">
    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
    <button><i class="fa fa-search"></i></button>
  </form>

  <div class="nav-actions">
    <a href="cart.php">
      <i class="fa fa-shopping-cart"></i>
      <span class="badge"><?= count($_SESSION['cart'] ?? []) ?></span>
    </a>
    <a href="orders.php"><i class="fa fa-box"></i></a>
    <a href="/public/logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<section class="hero">
  <h1>Smart Shopping Starts Here</h1>
  <p>Best prices • Fast delivery • Live tracking</p>
</section>

<div class="layout">

<aside class="sidebar">
  <h3>Filters</h3>
  <form method="get">
    <label>Category</label>
    <select name="category">
      <option value="">All</option>
      <?php while($c=mysqli_fetch_assoc($categories)): ?>
        <option value="<?= $c['name'] ?>" <?= $category===$c['name']?'selected':'' ?>>
          <?= htmlspecialchars($c['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Price Range</label>
    <div class="price-range">
      <input type="number" name="min" value="<?= $min ?>" placeholder="Min">
      <input type="number" name="max" value="<?= $max ?>" placeholder="Max">
    </div>

    <label>Sort</label>
    <select name="sort">
      <option value="">Default</option>
      <option value="low_high" <?= $sort==='low_high'?'selected':'' ?>>Low → High</option>
      <option value="high_low" <?= $sort==='high_low'?'selected':'' ?>>High → Low</option>
    </select>

    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
    <button class="apply">Apply</button>
  </form>
</aside>

<main class="content">
<?php if(mysqli_num_rows($products)===0): ?>
<div class="empty"><h3>No products found</h3></div>
<?php else: ?>
<div class="grid">
<?php while($p=mysqli_fetch_assoc($products)): ?>
<div class="card">
  <img src="../uploads/products/<?= $p['image'] ?>" onerror="this.src='../uploads/products/default.png'">
  <div class="info">
    <h4><?= htmlspecialchars($p['name']) ?></h4>
    <span class="cat"><?= htmlspecialchars($p['category']) ?></span>
    <div class="price">LKR <?= number_format($p['price'],2) ?></div>
    <form method="post" action="add-to-cart.php">
      <input type="hidden" name="id" value="<?= $p['id'] ?>">
      <button>Add to Cart</button>
    </form>
  </div>
</div>
<?php endwhile; ?>
</div>
<?php endif; ?>
</main>
</div>

<!-- ✅ MOBILE BOTTOM NAV -->
<nav class="mobile-nav">
  <a href="/customer/home.php"><i class="fa fa-home"></i><span>Home</span></a>
  <a href="/customer/cart.php"><i class="fa fa-shopping-cart"></i></a>
  <a href="/customer/orders.php"><i class="fa fa-box"></i><span>Orders</span></a>
  <a href="/public/logout.php"><i class="fa fa-sign-out-alt"></i><span>Logout</span></a>
</nav>

</body>
</html>
