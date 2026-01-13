<?php
require_once "../../app/config/db.php";
require_once "../../app/helpers/auth.php";
$pageTitle = "Reports";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports | ISDN</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<div class="main">
<?php include "../layout/header.php"; ?>

<div class="cards">

    <a class="card" href="products-report.php">
        <h4>Products Report</h4>
        <span>View Stock Summary</span>
    </a>

    <a class="card" href="orders-report.php">
        <h4>Orders Report</h4>
        <span>Order Performance</span>
    </a>

    <a class="card" href="drivers-report.php">
        <h4>Drivers Report</h4>
        <span>Delivery Team</span>
    </a>

</div>

</div>
</div>

</body>
</html>
