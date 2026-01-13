<?php
require_once "../app/config/db.php";
require_once "../app/helpers/auth.php";

$driver = mysqli_fetch_assoc(
    mysqli_query($conn,"
    SELECT id FROM drivers
    WHERE user_id=".$_SESSION['user_id'])
);

$q = mysqli_query($conn,"
SELECT * FROM orders
WHERE driver_id=".$driver['id']
);
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/main.css">
<title>My Orders</title>
</head>
<body>

<h2>My Deliveries</h2>

<table class="data-table">
<tr><th>Order</th><th>Status</th></tr>

<?php while($o=mysqli_fetch_assoc($q)): ?>
<tr>
<td>#<?= $o['id'] ?></td>
<td><?= $o['status'] ?></td>
</tr>
<?php endwhile; ?>

</table>
</body>
</html>
