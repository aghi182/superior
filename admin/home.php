<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Set current page for sidebar
$current_page = 'home';
$page_title = 'Edit Home';

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

  <?php include 'includes/scripts.php'; ?>
</body>
</html>