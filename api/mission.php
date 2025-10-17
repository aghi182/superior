<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM mission_content ORDER BY id LIMIT 1");
        $stmt->execute();
        $mission = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => $mission ?: null
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
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM mission_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $pdo->prepare("
                UPDATE mission_content 
                SET title = ?, description = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = 1
            ");
            $stmt->execute([$title, $description]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO mission_content (title, description) 
                VALUES (?, ?)
            ");
            $stmt->execute([$title, $description]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Mission updated successfully'
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
