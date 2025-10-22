<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Set current page for sidebar
$current_page = 'vision';
$page_title = 'Edit Vision';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $image_url = sanitizeInput($_POST['image_url'] ?? '');
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $image_url = 'pictures/' . $filename;
                }
            }
        }
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM vision_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE vision_content SET 
                title = ?, description = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = 1
            ");
            $stmt->execute([$title, $description, $image_url]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO vision_content (id, title, description, image_url)
                VALUES (1, ?, ?, ?)
            ");
            $stmt->execute([$title, $description, $image_url]);
        }
        
        $success_message = "Vision updated successfully!";
        
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}

// Get current data
try {
    $stmt = $pdo->prepare("SELECT * FROM vision_content WHERE id = 1");
    $stmt->execute();
    $vision = $stmt->fetch();
} catch (PDOException $e) {
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Vision - Admin Panel</title>
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
          <h2 class="mb-3"><i class="fas fa-eye me-2"></i>Edit Vision</h2>
          <p class="text-muted">Manage company vision content</p>
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
          <div class="col-lg-8">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Vision Content</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="title" class="form-label">Title</label>
                  <input type="text" class="form-control" id="title" name="title" 
                         value="<?php echo htmlspecialchars($vision['title'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <textarea class="form-control" id="description" name="description" 
                            rows="6" required><?php echo htmlspecialchars($vision['description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                  <label for="image" class="form-label">Image</label>
                  <input type="file" class="form-control" id="image" name="image" 
                         accept="image/*">
                  <?php if ($vision && $vision['image_url']): ?>
                  <div class="mt-2">
                    <small class="text-muted">Current: <?php echo htmlspecialchars($vision['image_url']); ?></small>
                    <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($vision['image_url']); ?>">
                  </div>
                  <?php endif; ?>
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
                <h6>Current Image:</h6>
                <?php if ($vision && $vision['image_url']): ?>
                <img src="../<?php echo htmlspecialchars($vision['image_url']); ?>" 
                     alt="Vision Preview" class="img-fluid rounded mb-3">
                <?php else: ?>
                <div class="text-center text-muted py-4">
                  <i class="fas fa-image fa-3x mb-2"></i>
                  <p>No image uploaded</p>
                </div>
                <?php endif; ?>
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