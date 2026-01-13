<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../public/index.php");
    exit;
}

/* SAFETY CHECK */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_POST['id'];
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
$price = (float)($_POST['price'] ?? 0);
$status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'inactive');

/* GET OLD IMAGE */
$res = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
if (mysqli_num_rows($res) === 0) {
    header("Location: index.php");
    exit;
}
$old = mysqli_fetch_assoc($res)['image'];

/* UPDATE BASIC DATA */
mysqli_query($conn, "
    UPDATE products SET
        name='$name',
        category='$category',
        price=$price,
        status='$status'
    WHERE id=$id
");

/* IMAGE UPDATE */
if (!empty($_FILES['image']['name'])) {

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if (in_array($ext, $allowed)) {

        $newImage = 'product_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../../uploads/products/" . $newImage
        );

        /* DELETE OLD IMAGE */
        if ($old && $old !== 'default.png' && file_exists("../../uploads/products/" . $old)) {
            unlink("../../uploads/products/" . $old);
        }

        mysqli_query($conn, "
            UPDATE products SET image='$newImage'
            WHERE id=$id
        ");
    }
}

header("Location: index.php");
exit;
