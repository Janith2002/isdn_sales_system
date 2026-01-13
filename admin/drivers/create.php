<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $contact  = trim($_POST['contact_number']);
    $password = $_POST['password'];

    if ($name === '' || $email === '' || $password === '') {
        $error = "All fields are required";
    } else {

        $check = mysqli_query($conn,"SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email already exists";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($conn,"
                INSERT INTO users (
                    name,
                    email,
                    contact_number,
                    password,
                    role,
                    status
                ) VALUES (
                    '$name',
                    '$email',
                    '$contact',
                    '$hash',
                    'driver',
                    'active'
                )
            ");

            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Driver</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

<div class="layout">
<?php include "../layout/sidebar.php"; ?>

<main class="main-content">
<?php $pageTitle="Add Driver"; include "../layout/header.php"; ?>

<form class="form-card" method="post">

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <label>Driver Name</label>
    <input name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <!-- ✅ ADDED (NO UI CHANGE) -->
    <label>Contact Number</label>
    <input name="contact_number" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button class="btn">Create Driver</button>

</form>

</main>
</div>

</body>
</html>
