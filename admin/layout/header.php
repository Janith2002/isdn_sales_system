<header class="top-header">

    <!-- MOBILE MENU BUTTON -->
    <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">☰</button>

    <div class="page-title">
        <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
    </div>

    <div class="header-right">
        <span class="role">System Administrator</span>
        <a href="/public/logout.php">Logout</a>
    </div>

</header>
