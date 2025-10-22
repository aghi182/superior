<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Set current page for sidebar
$current_page = 'mission';
$page_title = 'Edit Mission';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $quality = sanitizeInput($_POST['quality'] ?? '');
        $competitiveness = sanitizeInput($_POST['competitiveness'] ?? '');
        $concreteness = sanitizeInput($_POST['concreteness'] ?? '');
        $punctuality = sanitizeInput($_POST['punctuality'] ?? '');
        $satisfaction = sanitizeInput($_POST['satisfaction'] ?? '');
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM mission_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE mission_content SET 
                title = ?, description = ?, quality = ?, competitiveness = ?, 
                concreteness = ?, punctuality = ?, satisfaction = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = 1
            ");
            $stmt->execute([
                $title, $description, $quality, $competitiveness, 
                $concreteness, $punctuality, $satisfaction
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO mission_content (id, title, description, quality, competitiveness, 
                concreteness, punctuality, satisfaction)
                VALUES (1, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $title, $description, $quality, $competitiveness, 
                $concreteness, $punctuality, $satisfaction
            ]);
        }
        
        $success_message = "Mission updated successfully!";
        
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Get current data
try {
    $stmt = $pdo->prepare("SELECT * FROM mission_content WHERE id = 1");
    $stmt->execute();
    $mission = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Mission - Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <?php include 'includes/styles.php'; ?>
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
  <?php include 'includes/sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
      <div class="row">
        <div class="col-12">
          <h2 class="mb-3"><i class="fas fa-target me-2"></i>Edit Mission</h2>
          <p class="text-muted">Manage company mission content and values</p>
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
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-target me-2"></i>Mission Overview</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="title" class="form-label">Title</label>
                  <input type="text" class="form-control" id="title" name="title" 
                         value="<?php echo htmlspecialchars($mission['title'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <textarea class="form-control" id="description" name="description" 
                            rows="4"><?php echo htmlspecialchars($mission['description'] ?? ''); ?></textarea>
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-lg-6">
            <div class="card">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-star me-2"></i>Mission Values</h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="quality" class="form-label">Quality</label>
                    <input type="text" class="form-control" id="quality" name="quality" 
                           value="<?php echo htmlspecialchars($mission['quality'] ?? ''); ?>" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="competitiveness" class="form-label">Competitiveness</label>
                    <input type="text" class="form-control" id="competitiveness" name="competitiveness" 
                           value="<?php echo htmlspecialchars($mission['competitiveness'] ?? ''); ?>" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="concreteness" class="form-label">Concreteness</label>
                    <input type="text" class="form-control" id="concreteness" name="concreteness" 
                           value="<?php echo htmlspecialchars($mission['concreteness'] ?? ''); ?>" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="punctuality" class="form-label">Punctuality</label>
                    <input type="text" class="form-control" id="punctuality" name="punctuality" 
                           value="<?php echo htmlspecialchars($mission['punctuality'] ?? ''); ?>" required>
                  </div>
                  <div class="col-12 mb-3">
                    <label for="satisfaction" class="form-label">Satisfaction</label>
                    <input type="text" class="form-control" id="satisfaction" name="satisfaction" 
                           value="<?php echo htmlspecialchars($mission['satisfaction'] ?? ''); ?>" required>
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

  <?php include 'includes/scripts.php'; ?>
</body>
</html>