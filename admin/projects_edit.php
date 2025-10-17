<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: projects.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? '');
        $client = sanitizeInput($_POST['client'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $image_url = sanitizeInput($_POST['image_url'] ?? '');
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $image_url = 'pictures/' . $filename;
                } else {
                    throw new Exception('Failed to upload image');
                }
            } else {
                throw new Exception('Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS));
            }
        }
        
        $stmt = $pdo->prepare("
            UPDATE projects 
            SET title = ?, description = ?, image_url = ?, category = ?, 
                client = ?, status = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$title, $description, $image_url, $category, $client, $status, $sort_order, $id]);
        
        $success_message = 'Project updated successfully!';
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get project data
try {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    
    if (!$project) {
        header('Location: projects.php');
        exit();
    }
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Project - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
    .image-preview { max-width: 200px; max-height: 200px; object-fit: cover; }
  </style>
</head>
<body>
  <div class="container mt-4">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><i class="fas fa-edit me-2"></i>Edit Project</h2>
          <a href="projects.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Projects
          </a>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Project Information</h5>
          </div>
          <div class="card-body">
            <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
              <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
              <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
              <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($project['image_url']); ?>">
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="title" class="form-label">Title *</label>
                  <input type="text" class="form-control" id="title" name="title" 
                         value="<?php echo htmlspecialchars($project['title']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="category" class="form-label">Category</label>
                  <input type="text" class="form-control" id="category" name="category" 
                         value="<?php echo htmlspecialchars($project['category']); ?>" 
                         placeholder="e.g., Mechanical, Electrical">
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="client" class="form-label">Client</label>
                  <input type="text" class="form-control" id="client" name="client" 
                         value="<?php echo htmlspecialchars($project['client']); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="sort_order" class="form-label">Sort Order</label>
                  <input type="number" class="form-control" id="sort_order" name="sort_order" 
                         value="<?php echo $project['sort_order']; ?>" min="0">
                </div>
              </div>
              
              <div class="mb-3">
                <label for="description" class="form-label">Description *</label>
                <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($project['description']); ?></textarea>
              </div>
              
              <div class="mb-3">
                <label for="image" class="form-label">Project Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="text-muted">Leave empty to keep current image. Allowed: <?php echo implode(', ', ALLOWED_EXTENSIONS); ?></small>
                
                <?php if ($project['image_url']): ?>
                <div class="mt-2">
                  <p class="text-muted">Current image:</p>
                  <img src="../<?php echo htmlspecialchars($project['image_url']); ?>" 
                       alt="Current image" class="image-preview rounded">
                </div>
                <?php endif; ?>
                
                <div id="image-preview" class="mt-2" style="display: none;">
                  <p class="text-muted">New image preview:</p>
                  <img id="preview-img" class="image-preview rounded" alt="Preview">
                </div>
              </div>
              
              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                  <option value="1" <?php echo ($project['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                  <option value="0" <?php echo ($project['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
              </div>
              
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i>Update Project
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Preview</h5>
          </div>
          <div class="card-body">
            <div class="border rounded p-3 bg-light">
              <div id="preview-image" class="mb-3">
                <?php if ($project['image_url']): ?>
                <img src="../<?php echo htmlspecialchars($project['image_url']); ?>" 
                     alt="Current image" class="img-fluid rounded">
                <?php else: ?>
                <div class="bg-light p-3 rounded text-center text-muted">
                  <i class="fas fa-image fa-2x"></i>
                  <p class="mb-0">No image</p>
                </div>
                <?php endif; ?>
              </div>
              <h5 id="preview-title"><?php echo htmlspecialchars($project['title']); ?></h5>
              <p id="preview-description"><?php echo htmlspecialchars($project['description']); ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <span id="preview-category" class="badge bg-info" <?php echo $project['category'] ? '' : 'style="display: none;"'; ?>><?php echo htmlspecialchars($project['category']); ?></span>
                <small id="preview-client" class="text-muted" <?php echo $project['client'] ? '' : 'style="display: none;"'; ?>>Client: <?php echo htmlspecialchars($project['client']); ?></small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Live preview
    document.getElementById('title').addEventListener('input', function() {
      document.getElementById('preview-title').textContent = this.value;
    });
    
    document.getElementById('description').addEventListener('input', function() {
      document.getElementById('preview-description').textContent = this.value;
    });
    
    document.getElementById('category').addEventListener('input', function() {
      const preview = document.getElementById('preview-category');
      if (this.value) {
        preview.textContent = this.value;
        preview.style.display = 'inline-block';
      } else {
        preview.style.display = 'none';
      }
    });
    
    document.getElementById('client').addEventListener('input', function() {
      const preview = document.getElementById('preview-client');
      if (this.value) {
        preview.textContent = 'Client: ' + this.value;
        preview.style.display = 'block';
      } else {
        preview.style.display = 'none';
      }
    });
    
    // Image preview
    document.getElementById('image').addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const previewDiv = document.getElementById('preview-image');
          const previewImg = document.getElementById('preview-img');
          
          previewImg.src = e.target.result;
          previewDiv.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="img-fluid rounded">';
          document.getElementById('image-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
