<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$message = '';
$message_type = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = 'Semua field harus diisi!';
        $message_type = 'danger';
    } elseif ($new_password !== $confirm_password) {
        $message = 'Password baru dan konfirmasi password tidak sama!';
        $message_type = 'danger';
    } elseif (strlen($new_password) < 6) {
        $message = 'Password baru minimal 6 karakter!';
        $message_type = 'danger';
    } else {
        try {
            // Get current user data
            $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($current_password, $user['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password = ?, updated_at = datetime('now') WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                
                $message = 'Password berhasil diubah!';
                $message_type = 'success';
            } else {
                $message = 'Password lama salah!';
                $message_type = 'danger';
            }
        } catch (PDOException $e) {
            $message = 'Terjadi kesalahan: ' . $e->getMessage();
            $message_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - PT. Superior Teknik Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .btn-action {
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">
                            <i class="fas fa-cogs me-2"></i>Admin Panel
                        </h5>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link text-white" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link text-white" href="home.php">
                            <i class="fas fa-home me-2"></i>Edit Home
                        </a>
                        <a class="nav-link text-white" href="about.php">
                            <i class="fas fa-info-circle me-2"></i>Edit About
                        </a>
                        <a class="nav-link text-white" href="services.php">
                            <i class="fas fa-cogs me-2"></i>Edit Services
                        </a>
                        <a class="nav-link text-white" href="vision.php">
                            <i class="fas fa-eye me-2"></i>Edit Vision
                        </a>
                        <a class="nav-link text-white" href="mission.php">
                            <i class="fas fa-target me-2"></i>Edit Mission
                        </a>
                        <a class="nav-link text-white" href="contact.php">
                            <i class="fas fa-phone me-2"></i>Edit Contact
                        </a>
                        <a class="nav-link text-white" href="projects.php">
                            <i class="fas fa-project-diagram me-2"></i>Manage Projects
                        </a>
                        <a class="nav-link text-white" href="projects_add.php">
                            <i class="fas fa-plus me-2"></i>Add Project
                        </a>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a class="nav-link text-white" href="user_management.php">
                            <i class="fas fa-users me-2"></i>User Management
                        </a>
                        <?php endif; ?>
                        <hr class="text-white">
                        <a class="nav-link text-white" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                        <a class="nav-link text-white" href="../login.php?logout=1">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-key me-2"></i>Change Password</h1>
                    <div>
                        <a href="../dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>

                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Change Your Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <div class="form-text">Password minimal 6 karakter</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-action">
                                            <i class="fas fa-save me-2"></i>Change Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
