<?php
session_start();
require_once __DIR__ . '/../app/config/db.php';

$email    = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['login_error'] = "Please enter email and password";
    header("Location: index.php");
    exit;
}

$q = mysqli_query($conn, "
    SELECT id, email, password, role
    FROM users
    WHERE email='$email'
    LIMIT 1
");

if ($q && mysqli_num_rows($q) === 1) {

    $user = mysqli_fetch_assoc($q);

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

$_SESSION['login_error'] = "Invalid email or password";
header("Location: index.php");
exit;
