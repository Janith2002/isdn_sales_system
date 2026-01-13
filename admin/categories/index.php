<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

/* ADD CATEGORY */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);

    if ($name !== '') {
        $safe = mysqli_real_escape_string($conn, $name);
        mysqli_query($conn,"
            INSERT IGNORE INTO categories (name)
            VALUES ('$safe')
        ");
    }
    header("Location: index.php");
    exit;
}

/* DELETE CATEGORY */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    /* OPTIONAL SAFETY: prevent delete if used */
    $check = mysqli_fetch_assoc(
        mysqli_query($conn,"
            SELECT COUNT(*) total
            FROM products
            WHERE category = (
                SELECT name FROM categories WHERE id=$id
            )
        ")
    )['total'];

    if ($check == 0) {
        mysqli_query($conn,"DELETE FROM categories WHERE id=$id");
    }

    header("Location: index.php");
    exit;
}

/* FETCH CATEGORIES */
$cats = mysqli_query($conn,"
    SELECT * FROM categories
    ORDER BY name
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Categories</title>

<link rel="stylesheet" href="../../assets/css/admin.css">

<style>
.page-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
.form-card{
  background:#fff;
  padding:22px;
  border-radius:18px;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
  margin-bottom:26px;
}
.form-card input{
  width:100%;
  padding:12px 14px;
  border-radius:12px;
  border:2px solid #e5e7eb;
}
.form-card input:focus{
  outline:none;
  border-color:#ff7a2f;
}
.grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
  gap:22px;
}
.card{
  background:#fff;
  padding:20px;
  border-radius:18px;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
}
.delete{
  color:#dc2626;
  font-weight:600;
  text-decoration:none;
}
</style>
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Categories"; include "../layout/header.php"; ?>

<!-- PAGE HEADER -->
<div class="page-header">
  <h2>Category Management</h2>
</div>

<!-- ADD CATEGORY -->
<div class="form-card">
  <h3>Add Category</h3>
  <form method="post">
    <input type="text" name="name" placeholder="Category name" required>
    <br><br>
    <button class="btn primary">Add Category</button>
  </form>
</div>

<!-- CATEGORY LIST -->
<div class="grid">
<?php while($c = mysqli_fetch_assoc($cats)): ?>
  <div class="card">
    <strong><?= htmlspecialchars($c['name']) ?></strong>
    <br><br>
    <a href="?delete=<?= $c['id'] ?>"
       class="delete"
       onclick="return confirm('Delete this category?')">
       Delete
    </a>
  </div>
<?php endwhile; ?>
</div>

</main>
</div>

</body>
</html>
