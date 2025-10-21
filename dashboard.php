<?php
require_once 'config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get dashboard statistics
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projects");
    $stmt->execute();
    $total_projects = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM services");
    $stmt->execute();
    $total_services = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM contact_content");
    $stmt->execute();
    $total_contacts = $stmt->fetch()['count'];
    
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - PT. Superior Teknik Indonesia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .navbar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
    }
    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    .btn-action {
      border-radius: 10px;
      padding: 10px 20px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .sidebar {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      z-index: 1000;
      transition: all 0.3s ease;
      display: block !important;
    }
    .sidebar.collapsed {
      width: 60px;
    }
    .sidebar .nav-link {
      color: white;
      padding: 15px 20px;
      border-radius: 0;
      transition: all 0.3s ease;
    }
    .sidebar .nav-link:hover {
      background: rgba(255,255,255,0.1);
      color: white;
    }
    .sidebar .nav-link.active {
      background: rgba(255,255,255,0.2);
      color: white;
    }
    .main-content {
      margin-left: 250px;
      transition: all 0.3s ease;
    }
    .main-content.expanded {
      margin-left: 60px;
    }
    .sidebar-toggle {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 1001;
      background: rgba(255,255,255,0.2);
      border: none;
      color: white;
      padding: 10px;
      border-radius: 5px;
    }
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        display: block !important;
      }
      .sidebar.show {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0;
      }
    }
    
    @media (min-width: 769px) {
      .sidebar {
        display: block !important;
        transform: translateX(0);
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="p-3">
      <h5 class="text-white mb-4">
        <i class="fas fa-cogs me-2"></i>
        <span class="sidebar-text">Admin Panel</span>
      </h5>
    </div>
    <nav class="nav flex-column">
      <a class="nav-link active" href="dashboard.php">
        <i class="fas fa-tachometer-alt me-2"></i>
        <span class="sidebar-text">Dashboard</span>
      </a>
      <a class="nav-link" href="admin/home.php">
        <i class="fas fa-home me-2"></i>
        <span class="sidebar-text">Edit Home</span>
      </a>
      <a class="nav-link" href="admin/about.php">
        <i class="fas fa-info-circle me-2"></i>
        <span class="sidebar-text">Edit About</span>
      </a>
      <a class="nav-link" href="admin/services.php">
        <i class="fas fa-cogs me-2"></i>
        <span class="sidebar-text">Edit Services</span>
      </a>
      <a class="nav-link" href="admin/vision.php">
        <i class="fas fa-eye me-2"></i>
        <span class="sidebar-text">Edit Vision</span>
      </a>
      <a class="nav-link" href="admin/mission.php">
        <i class="fas fa-target me-2"></i>
        <span class="sidebar-text">Edit Mission</span>
      </a>
      <a class="nav-link" href="admin/contact.php">
        <i class="fas fa-phone me-2"></i>
        <span class="sidebar-text">Edit Contact</span>
      </a>
      <a class="nav-link" href="admin/projects.php">
        <i class="fas fa-project-diagram me-2"></i>
        <span class="sidebar-text">Manage Projects</span>
      </a>
      <a class="nav-link" href="admin/projects_add.php">
        <i class="fas fa-plus me-2"></i>
        <span class="sidebar-text">Add Project</span>
      </a>
      <?php if ($_SESSION['role'] === 'admin'): ?>
      <a class="nav-link" href="admin/user_management.php">
        <i class="fas fa-users me-2"></i>
        <span class="sidebar-text">User Management</span>
      </a>
      <?php endif; ?>
      <hr class="text-white">
      <a class="nav-link" href="index.php" target="_blank">
        <i class="fas fa-external-link-alt me-2"></i>
        <span class="sidebar-text">View Website</span>
      </a>
      <a class="nav-link" href="admin/change_password.php">
        <i class="fas fa-key me-2"></i>
        <span class="sidebar-text">Change Password</span>
      </a>
      <a class="nav-link" href="login.php?logout=1">
        <i class="fas fa-sign-out-alt me-2"></i>
        <span class="sidebar-text">Logout</span>
      </a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">
        <button class="btn sidebar-toggle d-lg-none" id="sidebarToggle">
          <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand ms-3" href="#">
          <i class="fas fa-cogs me-2"></i>Admin Dashboard
        </a>
        <div class="navbar-nav ms-auto">
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="admin/change_password.php">
                <i class="fas fa-key me-2"></i>Change Password
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="index.php" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>View Website
              </a></li>
              <li><a class="dropdown-item" href="login.php?logout=1">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
              </a></li>
            </ul>
          </div>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
    <div class="row">
      <div class="col-12">
        <h2 class="mb-3"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h2>
        <p class="text-muted">Kelola konten website PT. Superior Teknik Indonesia</p>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row my-4">
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body text-center">
            <h3 class="mb-0"><?php echo $total_projects; ?></h3>
            <small><i class="fas fa-project-diagram me-1"></i>Total Projects</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body text-center">
            <h3 class="mb-0"><?php echo $total_services; ?></h3>
            <small><i class="fas fa-cogs me-1"></i>Services</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body text-center">
            <h3 class="mb-0"><?php echo $total_contacts; ?></h3>
            <small><i class="fas fa-users me-1"></i>Contact Persons</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card stat-card">
          <div class="card-body text-center">
            <h3 class="mb-0">1</h3>
            <small><i class="fas fa-building me-1"></i>Company Profile</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 mb-2">
                <a href="admin/projects_add.php" class="btn btn-primary btn-action w-100">
                  <i class="fas fa-plus me-2"></i>Add Project
                </a>
              </div>
              <div class="col-md-3 mb-2">
                <a href="admin/projects.php" class="btn btn-info btn-action w-100">
                  <i class="fas fa-list me-2"></i>Manage Projects
                </a>
              </div>
              <div class="col-md-3 mb-2">
                <a href="admin/about.php" class="btn btn-success btn-action w-100">
                  <i class="fas fa-edit me-2"></i>Edit About
                </a>
              </div>
              <div class="col-md-3 mb-2">
                <a href="admin/contact.php" class="btn btn-warning btn-action w-100">
                  <i class="fas fa-phone me-2"></i>Update Contact
                </a>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-3 mb-2">
                <a href="admin/home.php" class="btn btn-secondary btn-action w-100">
                  <i class="fas fa-home me-2"></i>Edit Home
                </a>
              </div>
              <div class="col-md-3 mb-2">
                <a href="admin/services.php" class="btn btn-dark btn-action w-100">
                  <i class="fas fa-cogs me-2"></i>Edit Services
                </a>
              </div>
              <div class="col-md-3 mb-2">
                <a href="admin/vision.php" class="btn btn-primary btn-action w-100">
                  <i class="fas fa-eye me-2"></i>Edit Vision
                </a>
              </div>
              <div class="col-md-3 mb-2">
                <a href="admin/mission.php" class="btn btn-success btn-action w-100">
                  <i class="fas fa-target me-2"></i>Edit Mission
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div> <!-- Close main-content div -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');
      const sidebarToggle = document.getElementById('sidebarToggle');
      
      // Ensure sidebar is always visible on desktop
      if (window.innerWidth > 768) {
        sidebar.style.display = 'block';
        sidebar.classList.remove('show');
      }
      
      // Mobile sidebar toggle
      if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
          if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
          }
        });
      }
      
      // Close sidebar when clicking outside on mobile
      document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
          if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('show');
          }
        }
      });
      
      // Handle window resize
      window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
          sidebar.style.display = 'block';
          sidebar.classList.remove('show');
        } else {
          sidebar.style.display = 'block';
        }
      });
    });
  </script>
</body>
</html>
