<?php
session_start();
require_once "../../app/config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$drivers = mysqli_query($conn, "SELECT * FROM users WHERE role='driver'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Driver Management</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Drivers"; include "../layout/header.php"; ?>

<div class="page-header">
    <h2>Driver Management</h2>
    <a href="create.php" class="btn">+ Add Driver</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while($d = mysqli_fetch_assoc($drivers)): ?>
        <tr>
            <td><?= htmlspecialchars($d['name']) ?></td>
            <td><?= $d['email'] ?></td>
            <td>
                <span class="badge <?= $d['status']=='active'?'green':'red' ?>">
                    <?= $d['status'] ?>
                </span>
            </td>
            <td>
                <a href="toggle.php?id=<?= $d['id'] ?>" class="link">
                    <?= $d['status']=='active'?'Deactivate':'Activate' ?>
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</main>
</div>

</body>
</html>
