<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get counts for dashboard
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM projects");
        $stmt->execute();
        $total_projects = $stmt->fetch()['count'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM services");
        $stmt->execute();
        $total_services = $stmt->fetch()['count'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM contact_content");
        $stmt->execute();
        $total_contacts = $stmt->fetch()['count'];
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_projects' => $total_projects,
                'total_services' => $total_services,
                'total_contacts' => $total_contacts
            ]
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
