<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Set current page for sidebar
$current_page = 'services';
$page_title = 'Edit Services';

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
  <?php include 'includes/styles.php'; ?>
</head>
<body>
  <?php include 'includes/sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <?php include 'includes/navbar.php'; ?>

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

  <?php include 'includes/scripts.php'; ?>
</body>
</html>