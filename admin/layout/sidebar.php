<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        ISDN
        <span>Admin Panel</span>
    </div>

    <nav class="sidebar-menu">

        <a href="/admin/dashboard/index.php">
            Dashboard
        </a>

        <a href="/admin/products/index.php">
            Products
        </a>

        <a href="/admin/orders/index.php">
            Orders
        </a>

        <a href="/admin/drivers/index.php">
            Drivers
        </a>

    </nav>

</aside>

<!-- MOBILE OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
</script>
