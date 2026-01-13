<?php
session_start();
require_once "../../app/config/db.php";
if ($_SESSION['role'] !== 'admin') exit;

/* PRODUCT DATA */
$sku   = mysqli_real_escape_string($conn,$_POST['sku']);
$name  = mysqli_real_escape_string($conn,$_POST['name']);
$cat   = mysqli_real_escape_string($conn,$_POST['category']);
$price = (float)$_POST['price'];
$qty   = (int)$_POST['quantity'];
$min   = (int)$_POST['min_quantity'];
$status = 'active';

/* IMAGE UPLOAD */
$image = 'default.png';

if (!empty($_FILES['image']['name'])) {

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];

    if (!in_array($ext, $allowed)) {
        die("Invalid image format");
    }

    /* COMPLICATED RANDOM NAME */
    $image = 'product_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "../../uploads/products/".$image
    );
}

/* INSERT PRODUCT */
mysqli_query($conn,"
INSERT INTO products
(sku,name,category,price,quantity,image,status,min_quantity)
VALUES
('$sku','$name','$cat',$price,$qty,'$image','$status',$min)
");

header("Location: index.php");
exit;
