<?php
session_start();

require_once "../app/config/db.php";
require_once "../vendor/autoload.php";

header("Content-Type: application/json");

/* ================= STRIPE SECRET (SECURE) ================= */
/* ❌ DO NOT hardcode API keys in source code */
$stripeSecretKey = getenv("STRIPE_SECRET_KEY");

if (!$stripeSecretKey) {
    echo json_encode(["error" => "Stripe configuration missing"]);
    exit;
}

\Stripe\Stripe::setApiKey($stripeSecretKey);

/* ================= READ ADDRESS FROM CHECKOUT ================= */
$input = json_decode(file_get_contents("php://input"), true);

if (
    empty($input['name']) ||
    empty($input['phone']) ||
    empty($input['address'])
) {
    echo json_encode(["error" => "Invalid address"]);
    exit;
}

/* ================= SAVE ADDRESS IN SESSION ================= */
$_SESSION['delivery'] = [
    'name'    => trim($input['name']),
    'phone'   => trim($input['phone']),
    'address' => trim($input['address'])
];

$selected = $_SESSION['checkout_items'] ?? [];
$cart     = $_SESSION['cart'] ?? [];

if (empty($selected)) {
    echo json_encode(["error" => "No items selected"]);
    exit;
}

$lineItems = [];

foreach ($selected as $id) {

    $id  = (int)$id;
    $qty = (int)($cart[$id] ?? 0);

    if ($qty <= 0) continue;

    $res = mysqli_query(
        $conn,
        "SELECT name, price FROM products WHERE id = $id LIMIT 1"
    );

    if (!$res || mysqli_num_rows($res) === 0) continue;

    $p = mysqli_fetch_assoc($res);

    /* Convert LKR → USD cents (example rate) */
    $usdAmount = round(($p['price'] / 300) * 100);

    $lineItems[] = [
        "price_data" => [
            "currency" => "usd",
            "product_data" => [
                "name" => $p['name']
            ],
            "unit_amount" => $usdAmount
        ],
        "quantity" => $qty
    ];
}

if (empty($lineItems)) {
    echo json_encode(["error" => "Invalid cart"]);
    exit;
}

/* ================= CREATE STRIPE SESSION ================= */
try {

    $session = \Stripe\Checkout\Session::create([
        "mode" => "payment",
        "line_items" => $lineItems,

        /* ✅ LIVE DOMAIN URLs */
        "success_url" => "https://isdnstore.infinityfreeapp.com/customer/success.php",
        "cancel_url"  => "https://isdnstore.infinityfreeapp.com/customer/cart.php"
    ]);

    echo json_encode([
        "id" => $session->id
    ]);

} catch (Exception $e) {

    echo json_encode([
        "error" => "Payment error: " . $e->getMessage()
    ]);
}
