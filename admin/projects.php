<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Set current page for sidebar
$current_page = 'projects';
$page_title = 'Manage Projects';

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

  <?php include 'includes/scripts.php'; ?>
</body>
</html>