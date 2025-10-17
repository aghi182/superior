<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_content ORDER BY id LIMIT 1");
        $stmt->execute();
        $contact = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => $contact ?: null
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
        $person1_name = sanitizeInput($_POST['person1_name'] ?? '');
        $person1_phone = sanitizeInput($_POST['person1_phone'] ?? '');
        $person1_status = isset($_POST['person1_status']) ? 1 : 0;
        $person2_name = sanitizeInput($_POST['person2_name'] ?? '');
        $person2_phone = sanitizeInput($_POST['person2_phone'] ?? '');
        $person2_status = isset($_POST['person2_status']) ? 1 : 0;
        $email = sanitizeInput($_POST['email'] ?? '');
        $text_wa = sanitizeInput($_POST['text_wa'] ?? '');
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM contact_content WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            $stmt = $pdo->prepare("
                UPDATE contact_content 
                SET title = ?, person1_name = ?, person1_phone = ?, person1_status = ?, 
                    person2_name = ?, person2_phone = ?, person2_status = ?, 
                    email = ?, text_wa = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = 1
            ");
            $stmt->execute([$title, $person1_name, $person1_phone, $person1_status, 
                           $person2_name, $person2_phone, $person2_status, $email, $text_wa]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO contact_content (title, person1_name, person1_phone, person1_status, 
                                           person2_name, person2_phone, person2_status, email, text_wa) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $person1_name, $person1_phone, $person1_status, 
                           $person2_name, $person2_phone, $person2_status, $email, $text_wa]);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Contact updated successfully'
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
