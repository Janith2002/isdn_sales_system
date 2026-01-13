<?php
session_start();
require_once "../app/config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: /public/index.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$items = [];

if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));

    $q = mysqli_query($conn,"
        SELECT id, name, price, image
        FROM products
        WHERE id IN ($ids)
    ");

    while ($p = mysqli_fetch_assoc($q)) {
        $items[] = [
            'id'       => $p['id'],
            'name'     => $p['name'],
            'price'    => $p['price'],
            'image'    => $p['image'],
            'qty'      => $cart[$p['id']],
            'subtotal' => $p['price'] * $cart[$p['id']]
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Shopping Cart</title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<nav class="navbar">
  <div class="logo">ISDN</div>
  <div class="nav-actions">
    <a href="home.php"><i class="fa fa-home"></i></a>
    <a href="orders.php"><i class="fa fa-box"></i></a>
    <a href="/public/logout.php"><i class="fa fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="layout">
<main class="content">

<h2>Shopping Cart</h2>

<?php if (empty($items)): ?>
  <div class="empty">
    <i class="fa fa-shopping-cart"></i>
    <h3>Your cart is empty</h3>
  </div>
<?php else: ?>

<form method="post" action="checkout.php">
<?php $total = 0; foreach ($items as $i): $total += $i['subtotal']; ?>

<div class="card">
  <div class="info cart-item">

    <input type="checkbox" name="checkout_items[]" value="<?= $i['id'] ?>" checked>

    <img src="../uploads/products/<?= htmlspecialchars($i['image']) ?>" onerror="this.src='../uploads/products/default.png'">

    <div class="cart-info">
      <strong><?= htmlspecialchars($i['name']) ?></strong>
      <p>LKR <?= number_format($i['price'],2) ?></p>

      <div class="qty">
        <a href="qty.php?action=dec&id=<?= $i['id'] ?>">−</a>
        <span><?= $i['qty'] ?></span>
        <a href="qty.php?action=inc&id=<?= $i['id'] ?>">+</a>
      </div>
    </div>

    <div class="cart-right">
      <strong>LKR <?= number_format($i['subtotal'],2) ?></strong><br><br>
      <a href="remove-cart.php?id=<?= $i['id'] ?>" class="apply">Remove</a>
    </div>

  </div>
</div>

<?php endforeach; ?>

<div class="cart-footer" style="margin-top:30px;display:flex;justify-content:space-between;align-items:center">
  <div class="total-box">
    <strong>Total</strong><br>
    <span style="font-size:22px;font-weight:800;color:#ff7a2f">
      LKR <?= number_format($total,2) ?>
    </span>
  </div>

  <button class="apply" style="max-width:320px">Proceed to Checkout</button>
</div>

</form>
<?php endif; ?>

</main>
</div>

<!-- ✅ MOBILE BOTTOM NAV -->
<nav class="mobile-nav">
  <a href="/customer/home.php"><i class="fa fa-home"></i><span>Home</span></a>
  <a href="/customer/cart.php"><i class="fa fa-shopping-cart"></i></a>
  <a href="/customer/orders.php"><i class="fa fa-box"></i><span>Orders</span></a>
  <a href="/public/logout.php"><i class="fa fa-sign-out-alt"></i><span>Logout</span></a>
</nav>

</body>
</html>
