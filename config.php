<?php
/**
 * Database Configuration
 * Koneksi ke SQLite database menggunakan PDO
 */

// Set error reporting untuk development (disable di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_PATH', __DIR__ . '/website.db');
define('UPLOAD_FOLDER', __DIR__ . '/pictures');
define('ALLOWED_EXTENSIONS', ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico']);

// Ensure upload folder exists
if (!file_exists(UPLOAD_FOLDER)) {
    mkdir(UPLOAD_FOLDER, 0755, true);
}

// Database connection
try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

/**
 * Helper function untuk validasi file upload
 */
function isAllowedFile($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($extension, ALLOWED_EXTENSIONS);
}

/**
 * Helper function untuk generate unique filename
 */
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $timestamp = time();
    $random = substr(md5(uniqid()), 0, 8);
    return $timestamp . '_' . $random . '.' . $extension;
}

/**
 * Helper function untuk set CORS headers
 */
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Content-Type: application/json; charset=utf-8');
}

/**
 * Helper function untuk handle OPTIONS request (CORS preflight)
 */
function handleCorsPreflight() {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        setCorsHeaders();
        http_response_code(200);
        exit();
    }
}

/**
 * Helper function untuk session management
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Helper function untuk check login status
 */
function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Helper function untuk require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized', 'message' => 'Login required']);
        exit();
    }
}

/**
 * Helper function untuk sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function untuk validate required fields
 */
function validateRequired($data, $requiredFields) {
    $missing = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    return $missing;
}
?>
