<?php
session_start();
require_once __DIR__ . '/../app/config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $q = mysqli_query($conn, "
        SELECT id, email, password, role
        FROM users
        WHERE email='$email'
        LIMIT 1
    ");

    if ($q && mysqli_num_rows($q) === 1) {

        $user = mysqli_fetch_assoc($q);

        // ✅ HASHED PASSWORD CHECK (DO NOT TOUCH)
        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: /admin/dashboard/index.php");
            } elseif ($user['role'] === 'driver') {
                header("Location: /driver/dashboard.php");
            } else {
                header("Location: /customer/home.php");
            }
            exit;
        }
    }

    $error = "Invalid email or password";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ISDN | Sign In</title>
<link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>

<div class="auth-layout">

    <!-- LEFT BRAND -->
    <section class="auth-brand">
        <div class="brand-content">
            <h1>ISDN</h1>
            <h3>Sales & Delivery Management</h3>
        </div>
    </section>

    <!-- RIGHT FORM -->
    <section class="auth-form">
        <div class="form-wrapper">
            <h2>Sign in</h2>

            <?php if ($error): ?>
                <div class="form-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit">Sign In</button>
            </form>

            <!-- ✅ SAFE ADDITION (CUSTOMER REGISTER LINK) -->
            <div class="form-footer">
                Don’t have an account?
                <a href="/public/register.php">Create account</a>
            </div>

        </div>
    </section>

</div>

</body>
</html>
