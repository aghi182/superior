<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = (int)($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid project ID']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        
        if (!$project) {
            http_response_code(404);
            echo json_encode(['error' => 'Project not found']);
        } else {
            echo json_encode([
                'success' => true,
                'data' => $project
            ]);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database error',
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid project ID']);
        exit();
    }
    
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
        
        echo json_encode([
            'success' => true,
            'message' => 'Project updated successfully'
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
