<?php
require_once __DIR__ . '/../../app/helpers/auth.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: /public/index.php");
    exit;
}

require_once __DIR__ . '/../../app/config/db.php';

/* ===== KPI ===== */
$totalProducts = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM products")
)['total'];

$totalDrivers = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM users WHERE role='driver'")
)['total'];

$lowStockCount = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM products WHERE quantity<=min_quantity")
)['total'];

/* ===== LOW STOCK ===== */
$lowStockProducts = mysqli_query($conn,"
    SELECT name,quantity,min_quantity 
    FROM products 
    WHERE quantity<=min_quantity
");

/* ===== CHART 1 ===== */
$stockData = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        SUM(quantity>min_quantity) ok_stock,
        SUM(quantity<=min_quantity) low_stock
    FROM products
"));

/* ===== CHART 2 ===== */
$labels=[];$values=[];
$q=mysqli_query($conn,"SELECT name,quantity FROM products ORDER BY quantity DESC LIMIT 5");
while($r=mysqli_fetch_assoc($q)){
    $labels[]=$r['name'];
    $values[]=(int)$r['quantity'];
}

/* ===== CHART 3 (ORDERS + REVENUE) ===== */
$orderChart = mysqli_query($conn,"
    SELECT 
        DATE(created_at) day,
        COUNT(id) orders,
        SUM(total) revenue
    FROM orders
    WHERE billing_status='paid'
    GROUP BY day
    ORDER BY day ASC
    LIMIT 14
");

$days=[];$orders=[];$revenue=[];
while($r=mysqli_fetch_assoc($orderChart)){
    $days[]=$r['day'];
    $orders[]=(int)$r['orders'];
    $revenue[]=(float)$r['revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard</title>

<link rel="stylesheet" href="/assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="layout">

<?php include __DIR__.'/../layout/sidebar.php'; ?>

<main class="main-content">
<?php $pageTitle="Dashboard"; include __DIR__.'/../layout/header.php'; ?>

<!-- KPI -->
<div class="kpi-grid">
  <div class="kpi-card"><p>Total Products</p><h2><?= $totalProducts ?></h2></div>
  <div class="kpi-card alert"><p>Low Stock</p><h2><?= $lowStockCount ?></h2></div>
  <div class="kpi-card"><p>Total Drivers</p><h2><?= $totalDrivers ?></h2></div>
</div>

<!-- CHARTS -->
<div class="chart-grid">

<div class="chart-card">
<h3>Stock Status</h3>
<div class="chart-wrap"><canvas id="stockChart"></canvas></div>
</div>

<div class="chart-card">
<h3>Top Products</h3>
<div class="chart-wrap"><canvas id="qtyChart"></canvas></div>
</div>

<div class="chart-card">
<h3>Orders & Revenue Trend</h3>
<div class="chart-wrap"><canvas id="orderChart"></canvas></div>
</div>

</div>

<!-- LOW STOCK -->
<div class="card">
<h3>⚠ Low Stock Products</h3>
<?php if(mysqli_num_rows($lowStockProducts)==0): ?>
<p class="muted">All good</p>
<?php else: ?>
<table class="table">
<tr><th>Product</th><th>Qty</th><th>Min</th></tr>
<?php while($p=mysqli_fetch_assoc($lowStockProducts)): ?>
<tr>
<td><?= htmlspecialchars($p['name']) ?></td>
<td class="danger"><?= $p['quantity'] ?></td>
<td><?= $p['min_quantity'] ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>
</div>

</main>
</div>

<script>
Chart.defaults.responsive = true;
Chart.defaults.maintainAspectRatio = false;

/* STOCK */
const stockChart = new Chart(
  document.getElementById('stockChart'),
  {
    type:'doughnut',
    data:{
      labels:['OK','Low'],
      datasets:[{
        data:[<?= (int)$stockData['ok_stock'] ?>,<?= (int)$stockData['low_stock'] ?>],
        backgroundColor:['#22c55e','#ef4444']
      }]
    },
    options:{plugins:{legend:{position:'bottom'}}}
  }
);

/* TOP PRODUCTS */
const qtyChart = new Chart(
  document.getElementById('qtyChart'),
  {
    type:'bar',
    data:{
      labels:<?= json_encode($labels) ?>,
      datasets:[{
        data:<?= json_encode($values) ?>,
        backgroundColor:'#ff7a45'
      }]
    },
    options:{
      plugins:{legend:{display:false}},
      scales:{y:{beginAtZero:true}}
    }
  }
);

/* ORDERS + REVENUE */
const orderChart = new Chart(
  document.getElementById('orderChart'),
  {
    type:'line',
    data:{
      labels:<?= json_encode($days) ?>,
      datasets:[
        {
          label:'Orders',
          data:<?= json_encode($orders) ?>,
          borderColor:'#2563eb',
          backgroundColor:'rgba(37,99,235,.15)',
          fill:true,
          tension:.4,
          yAxisID:'y'
        },
        {
          label:'Revenue',
          data:<?= json_encode($revenue) ?>,
          borderColor:'#ff7a45',
          backgroundColor:'rgba(255,122,69,.2)',
          fill:true,
          tension:.4,
          yAxisID:'y1'
        }
      ]
    },
    options:{
      interaction:{mode:'index',intersect:false},
      scales:{
        y:{beginAtZero:true},
        y1:{beginAtZero:true,position:'right',grid:{drawOnChartArea:false}}
      }
    }
  }
);

/* FORCE RESIZE */
window.addEventListener('resize',()=>{
  stockChart.resize();
  qtyChart.resize();
  orderChart.resize();
});
</script>

</body>
</html>
