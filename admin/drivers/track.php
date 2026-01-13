<?php
require_once "../app/config/db.php";
require_once "../app/helpers/auth.php";

$id = $_GET['id'];

/* Fake GPS update */
$lat = 6.90 + rand(-4,4)/100;
$lng = 79.85 + rand(-4,4)/100;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Live Tracking | ISDN</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>

<div class="page">

<h2>Live Delivery Tracking</h2>

<div class="card">
    <iframe
        width="100%"
        height="420"
        style="border:0;border-radius:12px"
        src="https://maps.google.com/maps?q=<?= $lat ?>,<?= $lng ?>&z=14&output=embed">
    </iframe>

    <p class="note">
        Location updates automatically during delivery.
    </p>
</div>

</div>
</body>
</html>
