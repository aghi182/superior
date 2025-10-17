<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM vision_content ORDER BY id LIMIT 1");
        $stmt->execute();
        $vision = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => $vision ?: null
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database error',
            'message' => $e->getMessage()
        ]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireLogin();
    
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
            $stmt = $pdo->prepare("
                UPDATE vision_content 
                SET title = ?, description = ?, image_url = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = 1
            ");
            $stmt->execute([$title, $description, $image_url]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO vision_content (title, description, image_url) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$title, $description, $image_url]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Vision updated successfully'
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
