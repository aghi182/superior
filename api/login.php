<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST; // Fallback untuk form data
    }
    
    $username = sanitizeInput($input['username'] ?? '');
    $password = sanitizeInput($input['password'] ?? '');
    
    // Simple hardcoded login - ganti dengan database authentication di production
    if ($username === 'admin' && $password === 'admin123') {
        startSecureSession();
        $_SESSION['logged_in'] = true;
        
        echo json_encode([
            'success' => true,
            'message' => 'Login berhasil!',
            'redirect' => 'dashboard.html'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Username atau password salah!'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
