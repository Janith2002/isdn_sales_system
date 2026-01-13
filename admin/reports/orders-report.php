<?php
require_once "../../app/config/db.php";
require_once "../../app/helpers/auth.php";

$q = mysqli_query($conn,"SELECT * FROM orders");
?>
<h2>Orders Report</h2>
<button onclick="window.print()">Print / PDF</button>

<table border="1" width="100%">
<tr><th>ID</th><th>Customer</th><th>Status</th><th>Total</th></tr>
<?php while($r=mysqli_fetch_assoc($q)): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= $r['customer_name'] ?></td>
<td><?= $r['status'] ?></td>
<td><?= $r['total'] ?></td>
</tr>
<?php endwhile; ?>
</table>
