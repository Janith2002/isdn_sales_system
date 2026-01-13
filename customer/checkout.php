<?php
session_start();
require_once "../app/config/db.php";
require_once "../app/helpers/auth.php";

/* ROLE CHECK (SAFE) */
if ($_SESSION['role'] !== 'customer') {
    header("Location: /public/index.php");
    exit;
}

$selected = $_POST['checkout_items'] ?? [];
if (empty($selected)) {
    die("No items selected");
}

$_SESSION['checkout_items'] = $selected;

$total = 0;
$items = [];

$ids = implode(',', array_map('intval',$selected));
$q = mysqli_query($conn,"SELECT id,name,price FROM products WHERE id IN ($ids)");

while ($p = mysqli_fetch_assoc($q)) {
    $qty = $_SESSION['cart'][$p['id']];
    $sub = $p['price'] * $qty;
    $total += $sub;
    $items[] = $p['name']." × ".$qty;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


<title>Checkout</title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://js.stripe.com/v3/"></script>

<style>
.checkout-grid{
  display:grid;
  grid-template-columns:1.3fr 1fr;
  gap:32px;
}
.field{
  margin-bottom:16px;
}
.field label{
  display:block;
  font-size:13px;
  font-weight:600;
  color:#374151;
  margin-bottom:6px;
}
.field input,
.field textarea{
  width:100%;
  padding:12px 14px;
  border-radius:12px;
  border:1px solid #ddd;
  font-size:14px;
}
.field textarea{resize:none}
</style>
</head>

<body>

<nav class="navbar">
  <div class="logo">ISDN</div>
  <div class="nav-actions">
    <a href="cart.php"><i class="fa fa-shopping-cart"></i></a>
    <a href="orders.php"><i class="fa fa-box"></i></a>
  </div>
</nav>

<div class="layout">
<main class="content">

<div class="checkout-grid">

<!-- LEFT: ADDRESS -->
<div class="card">
  <div class="info">
    <h2>Delivery Address</h2>

    <div class="field">
      <label>Recipient Name</label>
      <input type="text" id="name" placeholder="John Perera">
    </div>

    <div class="field">
      <label>Contact Number</label>
      <input type="text" id="phone" placeholder="+94 7X XXX XXXX">
    </div>

    <div class="field">
      <label>Full Delivery Address</label>
      <textarea id="address" rows="4"
        placeholder="House No, Street, City, District"></textarea>
    </div>

  </div>
</div>

<!-- RIGHT: SUMMARY -->
<div class="card">
  <div class="info">
    <h2>Order Summary</h2>

    <p><?= implode("<br>",$items) ?></p>

    <h3 style="margin-top:20px">
      Total: LKR <?= number_format($total,2) ?>
    </h3>

    <button id="payBtn" class="apply" style="margin-top:20px">
      Pay with Card
    </button>

    <p id="msg"></p>
  </div>
</div>

</div>

</main>
</div>

<script>
const stripe = Stripe("pk_test_51SmPBpL0r5RQJat2ST4hKKOoHOVRMkTcfjZ6i13yzAdc9P1OLLzHVjcx7b7uqXSskhch1ndsIsBshGtaP2BUlVrP00kxBD7Q70");

document.getElementById("payBtn").onclick = async () => {

  const address = {
    name: document.getElementById("name").value,
    phone: document.getElementById("phone").value,
    address: document.getElementById("address").value
  };

  if(!address.name || !address.phone || !address.address){
    alert("Please complete delivery address");
    return;
  }

  document.getElementById("msg").innerText="Redirecting to payment...";

  const res = await fetch("create-payment.php",{
    method:"POST",
    headers:{'Content-Type':'application/json'},
    body:JSON.stringify(address)
  });

  const data = await res.json();

  if(!data.id){
    alert("Payment session failed");
    return;
  }

  stripe.redirectToCheckout({ sessionId:data.id });
};
</script>

</body>
</html>
