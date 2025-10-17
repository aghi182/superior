<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? '');
        $client = sanitizeInput($_POST['client'] ?? '');
        $status = (int)($_POST['status'] ?? 1);
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $image_url = '';
        
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
            INSERT INTO projects (title, description, image_url, category, client, status, sort_order) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $description, $image_url, $category, $client, $status, $sort_order]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Project added successfully',
            'project_id' => $pdo->lastInsertId()
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
