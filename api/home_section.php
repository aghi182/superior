<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM home_content ORDER BY id LIMIT 1");
        $stmt->execute();
        $home = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => $home ?: null
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
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            $input = $_POST;
        }
        
        $hero_title1 = sanitizeInput($input['hero_title1'] ?? '');
        $hero_title2 = sanitizeInput($input['hero_title2'] ?? '');
        $hero_subtitle = sanitizeInput($input['hero_subtitle'] ?? '');
        $hero_button_text = sanitizeInput($input['hero_button_text'] ?? '');
        $hero_button_link = sanitizeInput($input['hero_button_link'] ?? '');
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM home_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE home_content 
                SET hero_title1 = ?, hero_title2 = ?, hero_subtitle = ?, 
                    hero_button_text = ?, hero_button_link = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = 1
            ");
            $stmt->execute([$hero_title1, $hero_title2, $hero_subtitle, $hero_button_text, $hero_button_link]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO home_content (hero_title1, hero_title2, hero_subtitle, hero_button_text, hero_button_link) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$hero_title1, $hero_title2, $hero_subtitle, $hero_button_text, $hero_button_link]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Home content updated successfully'
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
