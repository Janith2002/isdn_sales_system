<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /public/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Product</title>

<link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Add Product"; include "../layout/header.php"; ?>

<div class="admin-form-card">

<h3>Add New Product</h3>

<form action="store.php" method="post" enctype="multipart/form-data" class="admin-form">

<div class="admin-form-grid">

  <div class="admin-form-group">
    <label>SKU</label>
    <input type="text" name="sku" required>
  </div>

  <div class="admin-form-group">
    <label>Product Name</label>
    <input type="text" name="name" required>
  </div>

  <div class="admin-form-group">
    <label>Category</label>
    <input type="text" name="category" required>
  </div>

  <div class="admin-form-group">
    <label>Price (LKR)</label>
    <input type="number" step="0.01" name="price" required>
  </div>

  <div class="admin-form-group">
    <label>Quantity</label>
    <input type="number" name="quantity" required>
  </div>

  <div class="admin-form-group">
    <label>Minimum Quantity</label>
    <input type="number" name="min_quantity" required>
  </div>

  <div class="admin-form-group full">
    <label>Product Image</label>
    <input type="file" name="image" accept="image/*" required>
  </div>

</div>

<div class="admin-form-actions">
  <button type="submit" class="admin-btn">Save Product</button>
  <a href="index.php" class="admin-btn outline">Cancel</a>
</div>

</form>

</div>

</main>
</div>

</body>
</html>
