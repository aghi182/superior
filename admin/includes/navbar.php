<?php
// Navbar component untuk admin panel
// Parameter: $page_title - judul halaman yang ditampilkan di navbar
$page_title = $page_title ?? 'Admin Panel';
?>
<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <button class="btn sidebar-toggle d-lg-none" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <a class="navbar-brand ms-3" href="#">
      <i class="fas fa-cogs me-2"></i><?php echo htmlspecialchars($page_title); ?>
    </a>
    <div class="navbar-nav ms-auto">
      <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
        </a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="change_password.php">
            <i class="fas fa-key me-2"></i>Change Password
          </a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="../index.php" target="_blank">
            <i class="fas fa-external-link-alt me-2"></i>View Website
          </a></li>
          <li><a class="dropdown-item" href="../login.php?logout=1">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
          </a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

