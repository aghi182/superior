<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No image file uploaded or upload error');
        }
        
        $file = $_FILES['image'];
        
        if (!isAllowedFile($file['name'])) {
            throw new Exception('Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS));
        }
        
        $filename = generateUniqueFilename($file['name']);
        $upload_path = UPLOAD_FOLDER . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            echo json_encode([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'filename' => $filename,
                'url' => 'pictures/' . $filename,
                'size' => $file['size'],
                'type' => $file['type']
            ]);
        } else {
            throw new Exception('Failed to move uploaded file');
        }
        
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
