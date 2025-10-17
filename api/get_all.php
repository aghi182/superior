<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

try {
    $data = [];
    
    // Home content
    $stmt = $pdo->prepare("SELECT * FROM home_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['home'] = $stmt->fetch() ?: null;
    
    // About content
    $stmt = $pdo->prepare("SELECT * FROM about_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['about'] = $stmt->fetch() ?: null;
    
    // Contact content
    $stmt = $pdo->prepare("SELECT * FROM contact_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['contact'] = $stmt->fetch() ?: null;
    
    // Projects
    $stmt = $pdo->prepare("SELECT * FROM projects ORDER BY sort_order ASC, id DESC LIMIT 100");
    $stmt->execute();
    $data['projects'] = $stmt->fetchAll();
    
    // Services
    $stmt = $pdo->prepare("SELECT * FROM services ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['services'] = $stmt->fetch() ?: null;
    
    // Vision content
    $stmt = $pdo->prepare("SELECT * FROM vision_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['vision'] = $stmt->fetch() ?: null;
    
    // Mission content
    $stmt = $pdo->prepare("SELECT * FROM mission_content ORDER BY id LIMIT 1");
    $stmt->execute();
    $data['mission'] = $stmt->fetch() ?: null;
    
    echo json_encode($data);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage()
    ]);
}
?>
