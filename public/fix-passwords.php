<?php
require_once "../app/config/db.php";

/*
 This file will RESET ALL USER PASSWORDS
 to: admin123
 using PHP's password_hash()
*/

$newPassword = password_hash("admin123", PASSWORD_DEFAULT);

$q = mysqli_query($conn, "UPDATE users SET password='$newPassword'");

if ($q) {
    echo "✅ Passwords reset successfully.<br>";
    echo "All users password = <b>admin123</b>";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
