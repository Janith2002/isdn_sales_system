<?php
require_once "../app/config/db.php";

echo "DB CONNECTED<br>";

$q = mysqli_query($conn, "SELECT id,email,role FROM users");
if (!$q) {
    die("QUERY FAILED: " . mysqli_error($conn));
}

while ($r = mysqli_fetch_assoc($q)) {
    echo $r['email'] . " - " . $r['role'] . "<br>";
}
