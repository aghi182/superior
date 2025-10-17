<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Get projects data
try {
    $stmt = $pdo->prepare("SELECT * FROM projects ORDER BY sort_order ASC, id DESC");
    $stmt->execute();
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Projects - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    .main-content {
      margin-left: 250px;
      min-height: 100vh;
      transition: all 0.3s ease;
    }
    .sidebar .nav-link {
      color: white;
      padding: 15px 20px;
      border-radius: 0;
      transition: all 0.3s ease;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      background: rgba(255,255,255,0.1);
      color: white;
    }
    .sidebar .nav-link i {
      width: 20px;
      margin-right: 10px;
    }
    .sidebar-text {
      transition: opacity 0.3s ease;
    }
    .sidebar.collapsed .sidebar-text {
      opacity: 0;
    }
    .sidebar.collapsed {
      width: 60px;
    }
    .sidebar.collapsed .nav-link {
      text-align: center;
      padding: 15px 10px;
    }
    .sidebar.collapsed .nav-link i {
      margin-right: 0;
    }
    .sidebar-toggle {
      background: none;
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
      <a class="nav-link" href="../dashboard.php">
        <i class="fas fa-tachometer-alt me-2"></i>
        <span class="sidebar-text">Dashboard</span>
      </a>
      <a class="nav-link" href="home.php">
        <i class="fas fa-home me-2"></i>
        <span class="sidebar-text">Edit Home</span>
      </a>
      <a class="nav-link" href="about.php">
        <i class="fas fa-info-circle me-2"></i>
        <span class="sidebar-text">Edit About</span>
      </a>
      <a class="nav-link" href="services.php">
        <i class="fas fa-cogs me-2"></i>
        <span class="sidebar-text">Edit Services</span>
      </a>
      <a class="nav-link" href="vision.php">
        <i class="fas fa-eye me-2"></i>
        <span class="sidebar-text">Edit Vision</span>
      </a>
      <a class="nav-link" href="mission.php">
        <i class="fas fa-target me-2"></i>
        <span class="sidebar-text">Edit Mission</span>
      </a>
      <a class="nav-link" href="contact.php">
        <i class="fas fa-phone me-2"></i>
        <span class="sidebar-text">Edit Contact</span>
      </a>
      <a class="nav-link active" href="projects.php">
        <i class="fas fa-project-diagram me-2"></i>
        <span class="sidebar-text">Manage Projects</span>
      </a>
      <a class="nav-link" href="projects_add.php">
        <i class="fas fa-plus me-2"></i>
        <span class="sidebar-text">Add Project</span>
      </a>
      <hr class="text-white">
      <a class="nav-link" href="../index.php" target="_blank">
        <i class="fas fa-external-link-alt me-2"></i>
        <span class="sidebar-text">View Website</span>
      </a>
      <a class="nav-link" href="../login.php?logout=1">
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
          <i class="fas fa-project-diagram me-2"></i>Manage Projects
        </a>
        <div class="navbar-nav ms-auto">
          <a class="nav-link" href="../index.php" target="_blank">
            <i class="fas fa-external-link-alt me-1"></i>View Website
          </a>
          <a class="nav-link" href="../login.php?logout=1">
            <i class="fas fa-sign-out-alt me-1"></i>Logout
          </a>
        </div>
      </div>
    </nav>

    <div class="container mt-4">
      <div class="row">
        <div class="col-12">
          <h2 class="mb-3"><i class="fas fa-project-diagram me-2"></i>Manage Projects</h2>
          <p class="text-muted">View and manage all projects</p>
        </div>
      </div>

      <?php if (isset($error_message)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <div class="row mb-3">
        <div class="col-12">
          <a href="projects_add.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Project
          </a>
        </div>
      </div>

      <div class="row">
        <?php if ($projects): ?>
        <?php foreach ($projects as $project): ?>
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card h-100">
            <?php if ($project['image_url']): ?>
            <img src="../<?php echo htmlspecialchars($project['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($project['title']); ?>" 
                 class="card-img-top" 
                 style="height: 200px; object-fit: cover;">
            <?php else: ?>
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                 style="height: 200px;">
              <i class="fas fa-image fa-3x text-muted"></i>
            </div>
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
              <?php if ($project['client']): ?>
              <p class="card-text text-muted">
                <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($project['client']); ?>
              </p>
              <?php endif; ?>
              <p class="card-text"><?php echo htmlspecialchars($project['description']); ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge <?php echo $project['status'] ? 'bg-success' : 'bg-secondary'; ?>">
                  <?php echo $project['status'] ? 'Active' : 'Inactive'; ?>
                </span>
                <div>
                  <a href="projects_edit.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="col-12">
          <div class="text-center py-5">
            <i class="fas fa-project-diagram fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No projects found</h5>
            <p class="text-muted">Start by adding your first project.</p>
            <a href="projects_add.php" class="btn btn-primary">
              <i class="fas fa-plus me-2"></i>Add Project
            </a>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

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