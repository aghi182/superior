<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM about_content ORDER BY id LIMIT 1");
        $stmt->execute();
        $about = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => $about ?: null
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
        $image_name = null;
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            
            if (isAllowedFile($file['name'])) {
                $image_name = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $image_name;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Image uploaded successfully
                } else {
                    throw new Exception('Failed to upload image');
                }
            } else {
                throw new Exception('Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS));
            }
        }
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM about_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            if ($image_name) {
                // Update with new image
                $stmt = $pdo->prepare("
                    UPDATE about_content 
                    SET title = ?, description = ?, images = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = 1
                ");
                $stmt->execute([$title, $description, $image_name]);
            } else {
                // Update without changing image
                $stmt = $pdo->prepare("
                    UPDATE about_content 
                    SET title = ?, description = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = 1
                ");
                $stmt->execute([$title, $description]);
            }
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO about_content (title, description, images) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$title, $description, $image_name]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'About content updated successfully'
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
