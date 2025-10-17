<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

if (isLoggedIn()) {
    echo json_encode([
        'authenticated' => true,
        'message' => 'User is logged in'
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'authenticated' => false,
        'message' => 'User is not logged in'
    ]);
}
?>
