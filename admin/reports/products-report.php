<?php
require_once "../../app/config/db.php";
require_once "../../app/helpers/auth.php";
$pageTitle = "Products Report";

$result = mysqli_query($conn,"SELECT * FROM products ORDER BY name");
if (!$result) die(mysqli_error($conn));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products Report | ISDN</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<div class="main">
<?php include "../layout/header.php"; ?>

<h3>Products Stock Report</h3>

<button onclick="window.print()" class="btn">Print / Save PDF</button>

<table class="data-table" style="margin-top:20px">
<tr>
    <th>SKU</th>
    <th>Name</th>
    <th>Category</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Status</th>
</tr>

<?php while($p=mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $p['sku'] ?></td>
    <td><?= $p['name'] ?></td>
    <td><?= $p['category'] ?></td>
    <td>Rs <?= number_format($p['price'],2) ?></td>
    <td><?= $p['quantity'] ?></td>
    <td><?= ucfirst(str_replace('_',' ',$p['status'])) ?></td>
</tr>
<?php endwhile; ?>

</table>

</div>
</div>

</body>
</html>
