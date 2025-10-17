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
        $mechanical_title = sanitizeInput($_POST['mechanical_title'] ?? '');
        $mechanical_description = sanitizeInput($_POST['mechanical_description'] ?? '');
        $electrical_title = sanitizeInput($_POST['electrical_title'] ?? '');
        $electrical_description = sanitizeInput($_POST['electrical_description'] ?? '');
        $mechanical_image_url = sanitizeInput($_POST['mechanical_image_url'] ?? '');
        $electrical_image_url = sanitizeInput($_POST['electrical_image_url'] ?? '');
        
        // Handle mechanical image upload
        if (isset($_FILES['mechanical_image']) && $_FILES['mechanical_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['mechanical_image'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $mechanical_image_url = 'pictures/' . $filename;
                }
            }
        }
        
        // Handle electrical image upload
        if (isset($_FILES['electrical_image']) && $_FILES['electrical_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['electrical_image'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $electrical_image_url = 'pictures/' . $filename;
                }
            }
        }
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM services WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE services SET 
                mechanical_title = ?, mechanical_description = ?, 
                electrical_title = ?, electrical_description = ?,
                mechanical_image_url = ?, electrical_image_url = ?,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = 1
            ");
            $stmt->execute([
                $mechanical_title, $mechanical_description,
                $electrical_title, $electrical_description,
                $mechanical_image_url, $electrical_image_url
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO services (id, mechanical_title, mechanical_description, 
                electrical_title, electrical_description, mechanical_image_url, electrical_image_url)
                VALUES (1, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $mechanical_title, $mechanical_description,
                $electrical_title, $electrical_description,
                $mechanical_image_url, $electrical_image_url
            ]);
        }
        
        $success_message = "Services updated successfully!";
        
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Get current data
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = 1");
    $stmt->execute();
    $services = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Services - Admin Panel</title>
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
      <a class="nav-link active" href="services.php">
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
          <i class="fas fa-cogs me-2"></i>Edit Services
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
          <h2 class="mb-3"><i class="fas fa-cogs me-2"></i>Edit Services</h2>
          <p class="text-muted">Manage mechanical and electrical services content</p>
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

      <form method="POST" enctype="multipart/form-data">
        <div class="row">
          <!-- Mechanical Services -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Mechanical Services</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="mechanical_title" class="form-label">Title</label>
                  <input type="text" class="form-control" id="mechanical_title" name="mechanical_title" 
                         value="<?php echo htmlspecialchars($services['mechanical_title'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="mechanical_description" class="form-label">Description</label>
                  <textarea class="form-control" id="mechanical_description" name="mechanical_description" 
                            rows="4"><?php echo htmlspecialchars($services['mechanical_description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                  <label for="mechanical_image" class="form-label">Image 1</label>
                  <input type="file" class="form-control" id="mechanical_image" name="mechanical_image" 
                         accept="image/*">
                  <?php if ($services && $services['mechanical_image_url']): ?>
                  <div class="mt-2">
                    <small class="text-muted">Current: <?php echo htmlspecialchars($services['mechanical_image_url']); ?></small>
                    <input type="hidden" name="mechanical_image_url" value="<?php echo htmlspecialchars($services['mechanical_image_url']); ?>">
                  </div>
                  <?php endif; ?>
                </div>
                <div class="mb-3">
                  <label for="mechanical_image2" class="form-label">Image 2</label>
                  <input type="file" class="form-control" id="mechanical_image2" name="mechanical_image2" 
                         accept="image/*">
                  <?php if ($services && $services['mechanical_image2']): ?>
                  <div class="mt-2">
                    <small class="text-muted">Current: <?php echo htmlspecialchars($services['mechanical_image2']); ?></small>
                    <input type="hidden" name="mechanical_image2" value="<?php echo htmlspecialchars($services['mechanical_image2']); ?>">
                  </div>
                  <?php endif; ?>
                </div>
                <div class="mb-3">
                  <label for="mechanical_image3" class="form-label">Image 3</label>
                  <input type="file" class="form-control" id="mechanical_image3" name="mechanical_image3" 
                         accept="image/*">
                  <?php if ($services && $services['mechanical_image3']): ?>
                  <div class="mt-2">
                    <small class="text-muted">Current: <?php echo htmlspecialchars($services['mechanical_image3']); ?></small>
                    <input type="hidden" name="mechanical_image3" value="<?php echo htmlspecialchars($services['mechanical_image3']); ?>">
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Electrical Services -->
          <div class="col-lg-6 mb-4">
            <div class="card">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Electrical Services</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="electrical_title" class="form-label">Title</label>
                  <input type="text" class="form-control" id="electrical_title" name="electrical_title" 
                         value="<?php echo htmlspecialchars($services['electrical_title'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="electrical_description" class="form-label">Description</label>
                  <textarea class="form-control" id="electrical_description" name="electrical_description" 
                            rows="4"><?php echo htmlspecialchars($services['electrical_description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                  <label for="electrical_image" class="form-label">Image 1</label>
                  <input type="file" class="form-control" id="electrical_image" name="electrical_image" 
                         accept="image/*">
                  <?php if ($services && $services['electrical_image_url']): ?>
                  <div class="mt-2">
                    <small class="text-muted">Current: <?php echo htmlspecialchars($services['electrical_image_url']); ?></small>
                    <input type="hidden" name="electrical_image_url" value="<?php echo htmlspecialchars($services['electrical_image_url']); ?>">
                  </div>
                  <?php endif; ?>
                </div>
                <div class="mb-3">
                  <label for="electrical_image2" class="form-label">Image 2</label>
                  <input type="file" class="form-control" id="electrical_image2" name="electrical_image2" 
                         accept="image/*">
                  <?php if ($services && $services['electrical_image2']): ?>
                  <div class="mt-2">
                    <small class="text-muted">Current: <?php echo htmlspecialchars($services['electrical_image2']); ?></small>
                    <input type="hidden" name="electrical_image2" value="<?php echo htmlspecialchars($services['electrical_image2']); ?>">
                  </div>
                  <?php endif; ?>
                </div>
                <div class="mb-3">
                  <label for="electrical_image3" class="form-label">Image 3</label>
                  <input type="file" class="form-control" id="electrical_image3" name="electrical_image3" 
                         accept="image/*">
                  <?php if ($services && $services['electrical_image3']): ?>
                  <div class="mt-2">
                    <small class="text-muted">Current: <?php echo htmlspecialchars($services['electrical_image3']); ?></small>
                    <input type="hidden" name="electrical_image3" value="<?php echo htmlspecialchars($services['electrical_image3']); ?>">
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Preview Section -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-6">
                    <h6>Mechanical Services Preview:</h6>
                    <div class="border p-3 rounded bg-light mb-3">
                      <h5><?php echo htmlspecialchars($services['mechanical_title'] ?? 'Mechanical Services'); ?></h5>
                      <p><?php echo htmlspecialchars($services['mechanical_description'] ?? 'Mechanical services description'); ?></p>
                      <div class="row">
                        <div class="col-4">
                          <?php if ($services && $services['mechanical_image_url']): ?>
                          <img src="../<?php echo htmlspecialchars($services['mechanical_image_url']); ?>" 
                               alt="Mechanical Preview 1" class="img-fluid rounded" 
                               style="max-height: 100px; object-fit: cover;">
                          <?php else: ?>
                          <div class="text-center text-muted py-2">
                            <i class="fas fa-image fa-1x"></i>
                          </div>
                          <?php endif; ?>
                        </div>
                        <div class="col-4">
                          <?php if ($services && $services['mechanical_image2']): ?>
                          <img src="../<?php echo htmlspecialchars($services['mechanical_image2']); ?>" 
                               alt="Mechanical Preview 2" class="img-fluid rounded" 
                               style="max-height: 100px; object-fit: cover;">
                          <?php else: ?>
                          <div class="text-center text-muted py-2">
                            <i class="fas fa-image fa-1x"></i>
                          </div>
                          <?php endif; ?>
                        </div>
                        <div class="col-4">
                          <?php if ($services && $services['mechanical_image3']): ?>
                          <img src="../<?php echo htmlspecialchars($services['mechanical_image3']); ?>" 
                               alt="Mechanical Preview 3" class="img-fluid rounded" 
                               style="max-height: 100px; object-fit: cover;">
                          <?php else: ?>
                          <div class="text-center text-muted py-2">
                            <i class="fas fa-image fa-1x"></i>
                          </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <h6>Electrical Services Preview:</h6>
                    <div class="border p-3 rounded bg-light mb-3">
                      <h5><?php echo htmlspecialchars($services['electrical_title'] ?? 'Electrical Services'); ?></h5>
                      <p><?php echo htmlspecialchars($services['electrical_description'] ?? 'Electrical services description'); ?></p>
                      <div class="row">
                        <div class="col-4">
                          <?php if ($services && $services['electrical_image_url']): ?>
                          <img src="../<?php echo htmlspecialchars($services['electrical_image_url']); ?>" 
                               alt="Electrical Preview 1" class="img-fluid rounded" 
                               style="max-height: 100px; object-fit: cover;">
                          <?php else: ?>
                          <div class="text-center text-muted py-2">
                            <i class="fas fa-image fa-1x"></i>
                          </div>
                          <?php endif; ?>
                        </div>
                        <div class="col-4">
                          <?php if ($services && $services['electrical_image2']): ?>
                          <img src="../<?php echo htmlspecialchars($services['electrical_image2']); ?>" 
                               alt="Electrical Preview 2" class="img-fluid rounded" 
                               style="max-height: 100px; object-fit: cover;">
                          <?php else: ?>
                          <div class="text-center text-muted py-2">
                            <i class="fas fa-image fa-1x"></i>
                          </div>
                          <?php endif; ?>
                        </div>
                        <div class="col-4">
                          <?php if ($services && $services['electrical_image3']): ?>
                          <img src="../<?php echo htmlspecialchars($services['electrical_image3']); ?>" 
                               alt="Electrical Preview 3" class="img-fluid rounded" 
                               style="max-height: 100px; object-fit: cover;">
                          <?php else: ?>
                          <div class="text-center text-muted py-2">
                            <i class="fas fa-image fa-1x"></i>
                          </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
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