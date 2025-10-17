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
        $title = sanitizeInput($_POST['title'] ?? '');
        $person1_name = sanitizeInput($_POST['person1_name'] ?? '');
        $person1_phone = sanitizeInput($_POST['person1_phone'] ?? '');
        $person1_status = isset($_POST['person1_status']) ? 1 : 0;
        $person2_name = sanitizeInput($_POST['person2_name'] ?? '');
        $person2_phone = sanitizeInput($_POST['person2_phone'] ?? '');
        $person2_status = isset($_POST['person2_status']) ? 1 : 0;
        $email = sanitizeInput($_POST['email'] ?? '');
        $text_wa = sanitizeInput($_POST['text_wa'] ?? '');
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM contact_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE contact_content SET 
                title = ?, person1_name = ?, person1_phone = ?, person1_status = ?,
                person2_name = ?, person2_phone = ?, person2_status = ?,
                email = ?, text_wa = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = 1
            ");
            $stmt->execute([
                $title, $person1_name, $person1_phone, $person1_status,
                $person2_name, $person2_phone, $person2_status,
                $email, $text_wa
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO contact_content (id, title, person1_name, person1_phone, person1_status,
                person2_name, person2_phone, person2_status, email, text_wa)
                VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title, $person1_name, $person1_phone, $person1_status,
                $person2_name, $person2_phone, $person2_status,
                $email, $text_wa
            ]);
        }
        
        $success_message = "Contact information updated successfully!";
        
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Get current data
try {
    $stmt = $pdo->prepare("SELECT * FROM contact_content WHERE id = 1");
    $stmt->execute();
    $contact = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Contact - Admin Panel</title>
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
      <a class="nav-link active" href="contact.php">
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
          <i class="fas fa-phone me-2"></i>Edit Contact
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
          <h2 class="mb-3"><i class="fas fa-phone me-2"></i>Edit Contact Information</h2>
          <p class="text-muted">Manage contact information and status</p>
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
                <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Contact Information</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="title" class="form-label">Section Title</label>
                  <input type="text" class="form-control" id="title" name="title" 
                         value="<?php echo htmlspecialchars($contact['title'] ?? ''); ?>" required>
                </div>
                
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="person1_name" class="form-label">Person 1 Name</label>
                    <input type="text" class="form-control" id="person1_name" name="person1_name" 
                           value="<?php echo htmlspecialchars($contact['person1_name'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="person1_phone" class="form-label">Person 1 Phone</label>
                    <input type="text" class="form-control" id="person1_phone" name="person1_phone" 
                           value="<?php echo htmlspecialchars($contact['person1_phone'] ?? ''); ?>">
                  </div>
                </div>
                
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="person1_status" name="person1_status" 
                           <?php echo ($contact['person1_status'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="person1_status">
                      Show Person 1 on website
                    </label>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="person2_name" class="form-label">Person 2 Name</label>
                    <input type="text" class="form-control" id="person2_name" name="person2_name" 
                           value="<?php echo htmlspecialchars($contact['person2_name'] ?? ''); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="person2_phone" class="form-label">Person 2 Phone</label>
                    <input type="text" class="form-control" id="person2_phone" name="person2_phone" 
                           value="<?php echo htmlspecialchars($contact['person2_phone'] ?? ''); ?>">
                  </div>
                </div>
                
                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="person2_status" name="person2_status" 
                           <?php echo ($contact['person2_status'] ?? 0) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="person2_status">
                      Show Person 2 on website
                    </label>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" 
                         value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>">
                </div>
                
                <div class="mb-3">
                  <label for="text_wa" class="form-label">WhatsApp Message Text</label>
                  <input type="text" class="form-control" id="text_wa" name="text_wa" 
                         value="<?php echo htmlspecialchars($contact['text_wa'] ?? ''); ?>">
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
                <h6>Contact Information Preview:</h6>
                <div class="border p-3 rounded bg-light">
                  <h5><?php echo htmlspecialchars($contact['title'] ?? 'Hubungi Kami'); ?></h5>
                  <?php if ($contact && $contact['person1_name'] && $contact['person1_status']): ?>
                  <p><strong><?php echo htmlspecialchars($contact['person1_name']); ?></strong></p>
                  <p><?php echo htmlspecialchars($contact['person1_phone']); ?></p>
                  <?php endif; ?>
                  <?php if ($contact && $contact['person2_name'] && $contact['person2_status']): ?>
                  <p><strong><?php echo htmlspecialchars($contact['person2_name']); ?></strong></p>
                  <p><?php echo htmlspecialchars($contact['person2_phone']); ?></p>
                  <?php endif; ?>
                  <?php if ($contact && $contact['email']): ?>
                  <p><strong>Email:</strong> <?php echo htmlspecialchars($contact['email']); ?></p>
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