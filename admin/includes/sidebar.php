<?php
// Sidebar component untuk admin panel
// Parameter: $current_page - halaman yang sedang aktif
$current_page = $current_page ?? '';
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="p-3">
    <h5 class="text-white mb-4">
      <i class="fas fa-cogs me-2"></i>
      <span class="sidebar-text">Admin Panel</span>
    </h5>
  </div>
  <nav class="nav flex-column">
    <a class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="../dashboard.php">
      <i class="fas fa-tachometer-alt me-2"></i>
      <span class="sidebar-text">Dashboard</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>" href="home.php">
      <i class="fas fa-home me-2"></i>
      <span class="sidebar-text">Edit Home</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'about' ? 'active' : ''; ?>" href="about.php">
      <i class="fas fa-info-circle me-2"></i>
      <span class="sidebar-text">Edit About</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'services' ? 'active' : ''; ?>" href="services.php">
      <i class="fas fa-cogs me-2"></i>
      <span class="sidebar-text">Edit Services</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'vision' ? 'active' : ''; ?>" href="vision.php">
      <i class="fas fa-eye me-2"></i>
      <span class="sidebar-text">Edit Vision</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'mission' ? 'active' : ''; ?>" href="mission.php">
      <i class="fas fa-target me-2"></i>
      <span class="sidebar-text">Edit Mission</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'contact' ? 'active' : ''; ?>" href="contact.php">
      <i class="fas fa-phone me-2"></i>
      <span class="sidebar-text">Edit Contact</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'projects' ? 'active' : ''; ?>" href="projects.php">
      <i class="fas fa-project-diagram me-2"></i>
      <span class="sidebar-text">Manage Projects</span>
    </a>
    <a class="nav-link <?php echo $current_page === 'projects_add' ? 'active' : ''; ?>" href="projects_add.php">
      <i class="fas fa-plus me-2"></i>
      <span class="sidebar-text">Add Project</span>
    </a>
    <?php if ($_SESSION['role'] === 'admin'): ?>
    <a class="nav-link <?php echo $current_page === 'user_management' ? 'active' : ''; ?>" href="user_management.php">
      <i class="fas fa-users me-2"></i>
      <span class="sidebar-text">User Management</span>
    </a>
    <?php endif; ?>
    <hr class="text-white">
    <a class="nav-link" href="../index.php" target="_blank">
      <i class="fas fa-external-link-alt me-2"></i>
      <span class="sidebar-text">View Website</span>
    </a>
    <a class="nav-link" href="change_password.php">
      <i class="fas fa-key me-2"></i>
      <span class="sidebar-text">Change Password</span>
    </a>
    <a class="nav-link" href="../login.php?logout=1">
      <i class="fas fa-sign-out-alt me-2"></i>
      <span class="sidebar-text">Logout</span>
    </a>
  </nav>
</div>

