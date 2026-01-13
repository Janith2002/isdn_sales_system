<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

/* ADD CATEGORY */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $n = mysqli_real_escape_string($conn, $name);
        mysqli_query($conn, "INSERT IGNORE INTO categories (name) VALUES ('$n')");
    }
    header("Location: index.php");
    exit;
}

/* DELETE CATEGORY */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    header("Location: index.php");
    exit;
}

/* FETCH */
$cats = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html>
<head>
<title>Categories</title>
<link rel="stylesheet" href="../../assets/css/admin.css">
<style>
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
.card h4{margin:0}
.delete{
  color:#dc2626;
  font-weight:600;
  text-decoration:none;
}
.form-card{
  background:#fff;
  padding:22px;
  border-radius:18px;
  box-shadow:0 10px 26px rgba(0,0,0,.08);
  margin-bottom:24px;
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
</style>
</head>

<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Categories"; include "../layout/header.php"; ?>

<!-- ADD CATEGORY -->
<div class="form-card">
  <h3>Add Category</h3>
  <form method="post">
    <input type="text" name="name" placeholder="Category name" required>
    <br><br>
    <button class="btn primary">Add Category</button>
  </form>
</div>

<!-- LIST -->
<div class="grid">
<?php while($c = mysqli_fetch_assoc($cats)): ?>
  <div class="card">
    <h4><?= htmlspecialchars($c['name']) ?></h4>
    <br>
    <a class="delete"
       href="?delete=<?= $c['id'] ?>"
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
