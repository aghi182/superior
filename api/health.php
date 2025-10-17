<?php
require_once '../config.php';

setCorsHeaders();
handleCorsPreflight();

$dbExists = file_exists(DB_PATH);
$status = $dbExists ? 'ok' : 'error';

echo json_encode([
    'status' => $status,
    'db' => $dbExists,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
