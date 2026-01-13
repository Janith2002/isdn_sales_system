<?php
session_start();
require_once "../app/config/db.php";

if ($_SESSION['role'] !== 'driver') {
    header("Location: ../public/index.php");
    exit;
}

$driverId = $_SESSION['user_id'];

$orders = mysqli_query($conn,"
    SELECT o.*
    FROM orders o
    WHERE o.driver_id = $driverId
    ORDER BY o.created_at DESC
");

if (!$orders) {
    die("DB Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Driver Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="layout">

<main class="main-content">

<h2>My Assigned Orders</h2>

<?php if (mysqli_num_rows($orders) === 0): ?>
    <p>No assigned orders yet.</p>
<?php else: ?>

<table class="table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Status</th>
            <th>Update Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

    <?php while ($o = mysqli_fetch_assoc($orders)): ?>
        <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= ucfirst(str_replace('_',' ',$o['status'])) ?></td>

            <td>
                <form method="post" action="update.php">
                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">

                    <select name="status" required>
                        <option value="">Select</option>

                        <?php if ($o['status'] === 'assigned'): ?>
                            <option value="picked">Picked</option>
                        <?php endif; ?>

                        <?php if (in_array($o['status'], ['picked','assigned'])): ?>
                            <option value="on_the_way">On the Way</option>
                        <?php endif; ?>

                        <?php if ($o['status'] !== 'delivered'): ?>
                            <option value="delivered">Delivered</option>
                        <?php endif; ?>
                    </select>
            </td>

            <td>
                    <button class="btn">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>

    </tbody>
</table>

<?php endif; ?>

</main>
</div>

</body>
</html>
