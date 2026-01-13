<?php
require_once "../app/config/db.php";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn,"SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists";
    } else {
        mysqli_query($conn,"
            INSERT INTO users (name,email,password,role,status)
            VALUES ('$name','$email','$password','customer','active')
        ");
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title>ISDN | Register</title>
<link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-layout">

    <section class="auth-brand">
        <div class="brand-content">
            <h1>ISDN</h1>
            <h3>Create your account</h3>
            <p>
                Register to place orders, track deliveries,
                and manage your purchases securely.
            </p>
        </div>
    </section>

    <section class="auth-form">
        <div class="form-wrapper">

            <h2>Create account</h2>

            <?php if ($error): ?>
                <div class="form-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">

                <div class="field">
                    <label>Full name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="field">
                    <label>Email address</label>
                    <input type="email" name="email" required>
                </div>

                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit">Create Account</button>
            </form>

            <div class="form-footer">
                Already have an account?
                <a href="index.php">Sign in</a>
            </div>

        </div>
    </section>

</div>


</body>
</html>
