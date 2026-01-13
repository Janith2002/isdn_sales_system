<?php
session_start();
require_once "../app/config/db.php";

/* ================= SECURITY ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../public/index.php");
    exit;
}

$userId   = (int)$_SESSION['user_id'];
$cart     = $_SESSION['cart'] ?? [];
$selected = $_SESSION['checkout_items'] ?? [];
$delivery = $_SESSION['delivery'] ?? null;

/* ================= VALIDATION ================= */
if (empty($cart) || empty($selected) || !$delivery) {
    header("Location: cart.php");
    exit;
}

/* ================= START TRANSACTION ================= */
mysqli_begin_transaction($conn);

try {

    $total  = 0;
    $items  = [];

    /* ===== FETCH PRODUCTS & CALCULATE TOTAL ===== */
    foreach ($selected as $pid) {

        $pid = (int)$pid;

        if (!isset($cart[$pid])) {
            throw new Exception("Invalid cart item");
        }

        $qty = (int)$cart[$pid];

        $q = mysqli_query($conn,"
            SELECT price
            FROM products
            WHERE id = $pid
            LIMIT 1
        ");

        if (!$q || mysqli_num_rows($q) === 0) {
            throw new Exception("Product not found");
        }

        $p = mysqli_fetch_assoc($q);

        $price = (float)$p['price'];
        $total += $price * $qty;

        $items[] = [
            'id'    => $pid,
            'qty'   => $qty,
            'price' => $price
        ];
    }

    /* ===== CREATE ORDER ===== */
    $orderInsert = mysqli_query($conn,"
        INSERT INTO orders (
            user_id,
            total,
            status,
            created_at,
            delivery_name,
            delivery_phone,
            delivery_address
        ) VALUES (
            $userId,
            $total,
            'pending',
            NOW(),
            '".mysqli_real_escape_string($conn,$delivery['name'])."',
            '".mysqli_real_escape_string($conn,$delivery['phone'])."',
            '".mysqli_real_escape_string($conn,$delivery['address'])."'
        )
    ");

    if (!$orderInsert) {
        throw new Exception("Order insert failed");
    }

    $orderId = mysqli_insert_id($conn);

    /* ===== INSERT ORDER ITEMS + UPDATE STOCK ===== */
    foreach ($items as $i) {

        mysqli_query($conn,"
            INSERT INTO order_items (order_id, product_id, qty, price)
            VALUES ($orderId, {$i['id']}, {$i['qty']}, {$i['price']})
        ");

        mysqli_query($conn,"
            UPDATE products
            SET quantity = quantity - {$i['qty']}
            WHERE id = {$i['id']}
        ");
    }

    /* ===== COMMIT ===== */
    mysqli_commit($conn);

    /* ================= CLEAN SESSION (SAFE) ================= */
    foreach ($selected as $pid) {
        unset($_SESSION['cart'][$pid]);
    }

    unset($_SESSION['checkout_items']);
    unset($_SESSION['delivery']);

    /* ================= REDIRECT ================= */
    header("Location: order-view.php?id=".$orderId);
    exit;

} catch (Exception $e) {

    mysqli_rollback($conn);

    // ❌ DO NOT destroy session
    // ❌ DO NOT logout user

    echo "Order failed. Please try again.";
}
