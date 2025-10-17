<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM services ORDER BY id LIMIT 1");
        $stmt->execute();
        $services = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => $services ?: null
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
        $mechanical_title = sanitizeInput($_POST['mechanical_title'] ?? '');
        $mechanical_description = sanitizeInput($_POST['mechanical_description'] ?? '');
        $electrical_title = sanitizeInput($_POST['electrical_title'] ?? '');
        $electrical_description = sanitizeInput($_POST['electrical_description'] ?? '');
        $mechanical_image_url = sanitizeInput($_POST['mechanical_image_url'] ?? '');
        $mechanical_image2 = sanitizeInput($_POST['mechanical_image2'] ?? '');
        $mechanical_image3 = sanitizeInput($_POST['mechanical_image3'] ?? '');
        $electrical_image_url = sanitizeInput($_POST['electrical_image_url'] ?? '');
        $electrical_image2 = sanitizeInput($_POST['electrical_image2'] ?? '');
        $electrical_image3 = sanitizeInput($_POST['electrical_image3'] ?? '');
        
        // Handle mechanical image uploads
        if (isset($_FILES['mechanical_image']) && $_FILES['mechanical_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['mechanical_image'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $mechanical_image_url = 'pictures/' . $filename;
                }
            }
        }
        
        if (isset($_FILES['mechanical_image2']) && $_FILES['mechanical_image2']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['mechanical_image2'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $mechanical_image2 = 'pictures/' . $filename;
                }
            }
        }
        
        if (isset($_FILES['mechanical_image3']) && $_FILES['mechanical_image3']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['mechanical_image3'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $mechanical_image3 = 'pictures/' . $filename;
                }
            }
        }
        
        // Handle electrical image uploads
        if (isset($_FILES['electrical_image']) && $_FILES['electrical_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['electrical_image'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $electrical_image_url = 'pictures/' . $filename;
                }
            }
        }
        
        if (isset($_FILES['electrical_image2']) && $_FILES['electrical_image2']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['electrical_image2'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $electrical_image2 = 'pictures/' . $filename;
                }
            }
        }
        
        if (isset($_FILES['electrical_image3']) && $_FILES['electrical_image3']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['electrical_image3'];
            
            if (isAllowedFile($file['name'])) {
                $filename = generateUniqueFilename($file['name']);
                $upload_path = UPLOAD_FOLDER . '/' . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    $electrical_image3 = 'pictures/' . $filename;
                }
            }
        }
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM services WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $pdo->prepare("
                UPDATE services 
                SET mechanical_title = ?, mechanical_description = ?, mechanical_image_url = ?, 
                    mechanical_image2 = ?, mechanical_image3 = ?,
                    electrical_title = ?, electrical_description = ?, electrical_image_url = ?, 
                    electrical_image2 = ?, electrical_image3 = ?,
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = 1
            ");
            $stmt->execute([$mechanical_title, $mechanical_description, $mechanical_image_url, 
                           $mechanical_image2, $mechanical_image3,
                           $electrical_title, $electrical_description, $electrical_image_url,
                           $electrical_image2, $electrical_image3]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO services (mechanical_title, mechanical_description, mechanical_image_url, 
                                    mechanical_image2, mechanical_image3,
                                    electrical_title, electrical_description, electrical_image_url,
                                    electrical_image2, electrical_image3) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$mechanical_title, $mechanical_description, $mechanical_image_url, 
                           $mechanical_image2, $mechanical_image3,
                           $electrical_title, $electrical_description, $electrical_image_url,
                           $electrical_image2, $electrical_image3]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Services updated successfully'
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
