<?php
require_once "../../app/config/db.php";
require_once "../../app/helpers/auth.php";
$pageTitle = "Drivers Report";

$result = mysqli_query($conn,"
    SELECT d.*,
    (SELECT COUNT(*) FROM orders o WHERE o.driver_id=d.id) AS total_orders
    FROM drivers d
");
if (!$result) die(mysqli_error($conn));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Drivers Report | ISDN</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<div class="main">
<?php include "../layout/header.php"; ?>

<h3>Drivers Performance Report</h3>

<button onclick="window.print()" class="btn">Print / Save PDF</button>

<table class="data-table" style="margin-top:20px">
<tr>
    <th>Name</th>
    <th>Phone</th>
    <th>Vehicle</th>
    <th>Status</th>
    <th>Orders Handled</th>
</tr>

<?php while($d=mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $d['name'] ?></td>
    <td><?= $d['phone'] ?></td>
    <td><?= $d['vehicle_no'] ?></td>
    <td><?= ucfirst($d['status']) ?></td>
    <td><?= $d['total_orders'] ?></td>
</tr>
<?php endwhile; ?>

</table>

</div>
</div>

</body>
</html>
