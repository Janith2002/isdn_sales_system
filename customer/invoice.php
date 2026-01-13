<?php
session_start();
require_once "../app/config/db.php";

/* ================= SECURITY ================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php");
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);
$userId  = (int)$_SESSION['user_id'];
$role    = $_SESSION['role'];

if ($orderId <= 0) {
    die("Invalid invoice request");
}

/* ================= FETCH ORDER ================= */
/* Admin → any order | Customer → own order only */
$where = ($role === 'admin')
    ? "o.id = $orderId"
    : "o.id = $orderId AND o.user_id = $userId";

$q = mysqli_query($conn,"
    SELECT o.*,
           u.name AS customer_name,
           u.email
    FROM orders o
    JOIN users u ON u.id = o.user_id
    WHERE $where
");

if (!$q || mysqli_num_rows($q) === 0) {
    die("Invoice not found or access denied");
}

$order = mysqli_fetch_assoc($q);

/* ================= FETCH ITEMS ================= */
$items = mysqli_query($conn,"
    SELECT p.name, oi.qty, oi.price
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = $orderId
");

$invoiceNo = "INV-" . str_pad($order['id'], 6, '0', STR_PAD_LEFT);

/* ================= PDF DOWNLOAD ================= */
if (isset($_GET['download'])) {

    require_once __DIR__ . "/../vendor/autoload.php";

    ob_clean(); // ✅ VERY IMPORTANT

    $html = '
    <html>
    <head>
    <style>
      body{font-family:DejaVu Sans,sans-serif;font-size:13px}
      h1{color:#ff7a2f}
      table{width:100%;border-collapse:collapse;margin-top:20px}
      th,td{padding:8px;border-bottom:1px solid #ddd}
      th{background:#f3f4f6}
      .total{text-align:right;font-size:18px;font-weight:bold;margin-top:20px}
    </style>
    </head>
    <body>

    <h1>Invoice</h1>

    <p>
      <strong>Invoice:</strong> '.$invoiceNo.'<br>
      <strong>Date:</strong> '.date("d M Y", strtotime($order['created_at'])).'
    </p>

    <p>
      <strong>Billed To:</strong><br>
      '.htmlspecialchars($order['customer_name']).'<br>
      '.htmlspecialchars($order['email']).'
    </p>

    <table>
      <tr>
        <th>Item</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Total</th>
      </tr>';

    mysqli_data_seek($items,0);
    while ($i = mysqli_fetch_assoc($items)) {
        $line = $i['qty'] * $i['price'];
        $html .= "
        <tr>
          <td>".htmlspecialchars($i['name'])."</td>
          <td>{$i['qty']}</td>
          <td>LKR ".number_format($i['price'],2)."</td>
          <td>LKR ".number_format($line,2)."</td>
        </tr>";
    }

    $html .= '
    </table>

    <div class="total">
      Total: LKR '.number_format($order['total'],2).'
    </div>

    </body>
    </html>';

    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream($invoiceNo.".pdf", ["Attachment" => true]);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<title>Invoice <?= $invoiceNo ?></title>

<link rel="stylesheet" href="../assets/css/customer-modern.css">

<style>
.invoice-box{
  max-width:900px;
  margin:30px auto;
  background:#fff;
  padding:30px;
  border-radius:22px;
  box-shadow:0 14px 34px rgba(0,0,0,.12)
}
table{width:100%;border-collapse:collapse;margin-top:20px}
th,td{padding:12px;border-bottom:1px solid #eee}
.total{text-align:right;font-size:22px;font-weight:800;color:#ff7a2f;margin-top:20px}
.actions{margin-top:24px;display:flex;gap:12px;flex-wrap:wrap}
.btn{
  padding:10px 18px;
  border-radius:14px;
  background:#ff7a2f;
  color:#fff;
  text-decoration:none;
  font-weight:600
}
.btn.outline{
  background:#fff;
  color:#ff7a2f;
  border:2px solid #ff7a2f
}
@media(max-width:600px){
  .actions{flex-direction:column}
  .btn{width:100%;text-align:center}
}
</style>
</head>

<body>

<div class="invoice-box">

<h2>Invoice</h2>
<p><strong><?= $invoiceNo ?></strong></p>
<p>Date: <?= date("d M Y", strtotime($order['created_at'])) ?></p>

<p>
<strong>Billed To:</strong><br>
<?= htmlspecialchars($order['customer_name']) ?><br>
<?= htmlspecialchars($order['email']) ?>
</p>

<table>
<tr>
  <th>Item</th>
  <th>Qty</th>
  <th>Price</th>
  <th>Total</th>
</tr>

<?php mysqli_data_seek($items,0); ?>
<?php while($i = mysqli_fetch_assoc($items)): ?>
<tr>
  <td><?= htmlspecialchars($i['name']) ?></td>
  <td><?= $i['qty'] ?></td>
  <td>LKR <?= number_format($i['price'],2) ?></td>
  <td>LKR <?= number_format($i['price']*$i['qty'],2) ?></td>
</tr>
<?php endwhile; ?>
</table>

<div class="total">
Total: LKR <?= number_format($order['total'],2) ?>
</div>

<div class="actions">
  <a href="invoice.php?id=<?= $orderId ?>&download=1" class="btn">
    Download PDF
  </a>

  <?php if ($role === 'admin'): ?>
    <a href="../admin/orders/index.php" class="btn outline">Back to Orders</a>
  <?php else: ?>
    <a href="orders.php" class="btn outline">Back to Orders</a>
  <?php endif; ?>
</div>

</div>

</body>
</html>
