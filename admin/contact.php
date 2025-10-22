<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Set current page for sidebar
$current_page = 'contact';
$page_title = 'Edit Contact';

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
                  <textarea class="form-control" id="text_wa" name="text_wa" rows="5"><?php echo htmlspecialchars($contact['text_wa'] ?? ''); ?></textarea>
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

  <?php include 'includes/scripts.php'; ?>
</body>
</html>