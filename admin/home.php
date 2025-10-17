<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $hero_title1 = sanitizeInput($_POST['hero_title1'] ?? '');
        $hero_title2 = sanitizeInput($_POST['hero_title2'] ?? '');
        $hero_subtitle = sanitizeInput($_POST['hero_subtitle'] ?? '');
        $hero_button_text = sanitizeInput($_POST['hero_button_text'] ?? '');
        $hero_button_link = sanitizeInput($_POST['hero_button_link'] ?? '');
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM home_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $pdo->prepare("
                UPDATE home_content 
                SET hero_title1 = ?, hero_title2 = ?, hero_subtitle = ?, 
                    hero_button_text = ?, hero_button_link = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = 1
            ");
            $stmt->execute([$hero_title1, $hero_title2, $hero_subtitle, $hero_button_text, $hero_button_link]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO home_content (hero_title1, hero_title2, hero_subtitle, hero_button_text, hero_button_link) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$hero_title1, $hero_title2, $hero_subtitle, $hero_button_text, $hero_button_link]);
        }
        
        $success_message = 'Home content updated successfully!';
        
    } catch (PDOException $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}

// Get current data
try {
    $stmt = $pdo->prepare("SELECT * FROM home_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $home = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Home - Admin Panel</title>
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
      <a class="nav-link active" href="../dashboard.php">
        <i class="fas fa-tachometer-alt me-2"></i>
        <span class="sidebar-text">Dashboard</span>
      </a>
      <a class="nav-link active" href="home.php">
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
      <a class="nav-link" href="projects.php">
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
          <i class="fas fa-home me-2"></i>Edit Home
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
          <h2 class="mb-3"><i class="fas fa-home me-2"></i>Edit Home Content</h2>
          <p class="text-muted">Manage hero section content</p>
        </div>
      </div>

      <?php if (isset($success_message)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <?php if (isset($error_message)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>

      <form method="POST">
        <div class="row">
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-home me-2"></i>Hero Section</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="hero_title1" class="form-label">Hero Title 1</label>
                  <input type="text" class="form-control" id="hero_title1" name="hero_title1" 
                         value="<?php echo htmlspecialchars($home['hero_title1'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="hero_title2" class="form-label">Hero Title 2</label>
                  <input type="text" class="form-control" id="hero_title2" name="hero_title2" 
                         value="<?php echo htmlspecialchars($home['hero_title2'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
                  <textarea class="form-control" id="hero_subtitle" name="hero_subtitle" 
                            rows="3" required><?php echo htmlspecialchars($home['hero_subtitle'] ?? ''); ?></textarea>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="hero_button_text" class="form-label">Button Text</label>
                    <input type="text" class="form-control" id="hero_button_text" name="hero_button_text" 
                           value="<?php echo htmlspecialchars($home['hero_button_text'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="hero_button_link" class="form-label">Button Link</label>
                    <input type="text" class="form-control" id="hero_button_link" name="hero_button_link" 
                           value="<?php echo htmlspecialchars($home['hero_button_link'] ?? ''); ?>">
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4">
            <div class="card">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Preview</h5>
              </div>
              <div class="card-body">
                <h6>Hero Section Preview:</h6>
                <div class="border p-3 rounded bg-light">
                  <h3><?php echo htmlspecialchars($home['hero_title1'] ?? 'Welcome'); ?></h3>
                  <h4 class="text-primary"><?php echo htmlspecialchars($home['hero_title2'] ?? 'Superior Teknik'); ?></h4>
                  <p class="text-muted"><?php echo htmlspecialchars($home['hero_subtitle'] ?? 'Engineering Excellence'); ?></p>
                  <?php if ($home && $home['hero_button_text']): ?>
                  <button class="btn btn-primary btn-sm"><?php echo htmlspecialchars($home['hero_button_text']); ?></button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="d-flex justify-content-between">
              <a href="../dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Changes
              </button>
            </div>
          </div>
        </div>
      </form>
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