<?php
require_once '../config.php';

// Check authentication
startSecureSession();
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../dashboard.php');
    exit();
}

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_user') {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = sanitizeInput($_POST['full_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $role = sanitizeInput($_POST['role'] ?? 'user');
        
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $message = 'Username sudah digunakan!';
                $message_type = 'danger';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, full_name, email, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, datetime('now'), datetime('now'))");
                $stmt->execute([$username, $hashed_password, $full_name, $email, $role]);
                $message = 'User berhasil ditambahkan!';
                $message_type = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Terjadi kesalahan: ' . $e->getMessage();
            $message_type = 'danger';
        }
    } elseif ($action === 'edit_user') {
        $user_id = (int)$_POST['user_id'];
        $username = sanitizeInput($_POST['username'] ?? '');
        $full_name = sanitizeInput($_POST['full_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $role = sanitizeInput($_POST['role'] ?? 'user');
        $status = (int)$_POST['status'];
        
        try {
            // Check if username already exists (excluding current user)
            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $user_id]);
            if ($stmt->fetch()) {
                $message = 'Username sudah digunakan!';
                $message_type = 'danger';
            } else {
                $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, full_name = ?, email = ?, role = ?, status = ?, updated_at = datetime('now') WHERE id = ?");
                $stmt->execute([$username, $full_name, $email, $role, $status, $user_id]);
                $message = 'User berhasil diupdate!';
                $message_type = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Terjadi kesalahan: ' . $e->getMessage();
            $message_type = 'danger';
        }
    } elseif ($action === 'delete_user') {
        $user_id = (int)$_POST['user_id'];
        
        // Prevent deleting own account
        if ($user_id == $_SESSION['user_id']) {
            $message = 'Tidak dapat menghapus akun sendiri!';
            $message_type = 'danger';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
                $stmt->execute([$user_id]);
                $message = 'User berhasil dihapus!';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Terjadi kesalahan: ' . $e->getMessage();
                $message_type = 'danger';
            }
        }
    }
}

// Get all users
try {
    $stmt = $pdo->query("SELECT id, username, full_name, email, role, status, created_at FROM admin_users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $message = 'Terjadi kesalahan saat mengambil data user: ' . $e->getMessage();
    $message_type = 'danger';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - PT. Superior Teknik Indonesia</title>
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
                        <a class="nav-link text-white active" href="user_management.php">
                            <i class="fas fa-users me-2"></i>User Management
                        </a>
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
                    <h1 class="h2"><i class="fas fa-users me-2"></i>User Management</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus me-2"></i>Add User
                    </button>
                </div>

                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $user['status'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_user">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="user_id" id="delete_user_id">
                        <p>Are you sure you want to delete user <strong id="delete_username"></strong>?</p>
                        <p class="text-danger">This action cannot be undone!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_full_name').value = user.full_name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_status').value = user.status;
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }
        
        function deleteUser(userId, username) {
            document.getElementById('delete_user_id').value = userId;
            document.getElementById('delete_username').textContent = username;
            
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }
    </script>
</body>
</html>
