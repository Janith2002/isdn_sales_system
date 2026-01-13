<?php
session_start();
require_once "../../app/config/db.php";

/* ================= SECURITY ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$res = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");

if (!$res || mysqli_num_rows($res) === 0) {
    header("Location: index.php");
    exit;
}

$p = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Product</title>

<link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Edit Product"; include "../layout/header.php"; ?>

<div class="admin-form-card">

<h3>Edit Product</h3>

<form action="update.php" method="post" enctype="multipart/form-data" class="admin-form">

<input type="hidden" name="id" value="<?= $p['id'] ?>">

<div class="admin-form-grid">

  <!-- PRODUCT NAME -->
  <div class="admin-form-group">
    <label>Product Name</label>
    <input type="text" name="name"
           value="<?= htmlspecialchars($p['name']) ?>" required>
  </div>

  <!-- CATEGORY -->
  <div class="admin-form-group">
    <label>Category</label>
    <input type="text" name="category"
           value="<?= htmlspecialchars($p['category']) ?>" required>
  </div>

  <!-- PRICE -->
  <div class="admin-form-group">
    <label>Price (LKR)</label>
    <input type="number" step="0.01" name="price"
           value="<?= $p['price'] ?>" required>
  </div>

  <!-- STATUS -->
  <div class="admin-form-group">
    <label>Status</label>
    <select name="status" required>
      <option value="active" <?= $p['status']=='active'?'selected':'' ?>>
        Active
      </option>
      <option value="inactive" <?= $p['status']=='inactive'?'selected':'' ?>>
        Inactive
      </option>
    </select>
  </div>

  <!-- IMAGE -->
  <div class="admin-form-group full">
    <label>Replace Product Image (optional)</label>
    <input type="file" name="image" accept="image/*">
    <small style="color:#6b7280">
      Leave empty to keep existing image
    </small>
  </div>

</div>

<!-- ACTIONS -->
<div class="admin-form-actions">
  <button type="submit" class="admin-btn">
    Update Product
  </button>

  <a href="index.php" class="admin-btn outline">
    Cancel
  </a>
</div>

</form>

</div>

</main>
</div>

</body>
</html>
