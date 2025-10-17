<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

startSecureSession();
session_destroy();

echo json_encode([
    'success' => true,
    'message' => 'Anda telah logout',
    'redirect' => 'index.html'
]);
?>
