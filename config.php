<?php
// ═══════════════════════════════════════════════════════════════
// VuaMXT - Configuration File
// ═══════════════════════════════════════════════════════════════

// Database Configuration
define('DB_FILE', __DIR__ . '/users.db');

// API Configuration
// Sử dụng biến môi trường để bảo mật sensitive data
define('RAILWAY_URL', getenv('RAILWAY_URL') ?: 'https://vuamxtapi.up.railway.app/api');
define('TAPLAYMA_API_TOKEN', getenv('TAPLAYMA_API_TOKEN') ?: '44895071-0d4f-4983-873b-15ddbeb045a4');

// Admin Configuration
define('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'MatKhauCuaBan123');

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('BYPASS_TOKEN_LIFETIME', 600); // 10 minutes
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// CORS Configuration
// Trong production, nên thay thế '*' bằng domain cụ thể
define('CORS_ALLOW_ORIGIN', getenv('CORS_ALLOW_ORIGIN') ?: '*');

// Rate Limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 60); // requests per minute
define('RATE_LIMIT_WINDOW', 60); // seconds

// Feature Limits (FREE users)
define('FREE_SPAM_LIMIT', 2);
define('FREE_BAN7_LIMIT', 1);
define('MAX_SPAM_DURATION_DAYS', 15);

// ═══════════════════════════════════════════════════════════════
// Helper Functions
// ═══════════════════════════════════════════════════════════════

/**
 * Initialize Database with proper schema
 */
function initDatabase() {
    try {
        $db = new PDO("sqlite:" . DB_FILE);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Create users table
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            email TEXT,
            password TEXT NOT NULL,
            is_pro INTEGER DEFAULT 0,
            spam_count INTEGER DEFAULT 0,
            ban7_count INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login DATETIME
        )");
        
        // Add ban7_count column if not exists (for backward compatibility)
        try {
            $db->query("SELECT ban7_count FROM users LIMIT 1");
        } catch (Exception $e) {
            $db->exec("ALTER TABLE users ADD COLUMN ban7_count INTEGER DEFAULT 0");
        }
        
        // Create keys_store table
        $db->exec("CREATE TABLE IF NOT EXISTS keys_store (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            key_code TEXT UNIQUE NOT NULL,
            is_used INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            used_at DATETIME
        )");
        
        // Create bypass_tokens table
        $db->exec("CREATE TABLE IF NOT EXISTS bypass_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            uid INTEGER NOT NULL,
            feature TEXT NOT NULL,
            token TEXT UNIQUE NOT NULL,
            is_used INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create rate_limit table
        $db->exec("CREATE TABLE IF NOT EXISTS rate_limits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            request_count INTEGER DEFAULT 1,
            window_start DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(ip_address, window_start)
        )");
        
        // Clean old rate limit records
        $db->exec("DELETE FROM rate_limits WHERE window_start < datetime('now', '-1 minute')");
        
        return $db;
    } catch (PDOException $e) {
        error_log("Database initialization error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Check rate limit for IP address
 */
function checkRateLimit($ip) {
    if (!RATE_LIMIT_ENABLED) {
        return true;
    }
    
    try {
        $db = initDatabase();
        
        // Clean old records first
        $db->exec("DELETE FROM rate_limits WHERE window_start < datetime('now', '-" . RATE_LIMIT_WINDOW . " seconds')");
        
        // Check current window
        $stmt = $db->prepare("SELECT request_count FROM rate_limits WHERE ip_address = ? AND window_start >= datetime('now', '-" . RATE_LIMIT_WINDOW . " seconds')");
        $stmt->execute([$ip]);
        $result = $stmt->fetch();
        
        if ($result && $result['request_count'] >= RATE_LIMIT_REQUESTS) {
            return false;
        }
        
        // Increment or create record
        if ($result) {
            $stmt = $db->prepare("UPDATE rate_limits SET request_count = request_count + 1 WHERE ip_address = ?");
            $stmt->execute([$ip]);
        } else {
            $stmt = $db->prepare("INSERT INTO rate_limits (ip_address, request_count) VALUES (?, 1)");
            $stmt->execute([$ip]);
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Rate limit check error: " . $e->getMessage());
        return true; // Allow on error to not block legitimate users
    }
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ip = '';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
}

/**
 * Send JSON response
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send error response
 */
function sendErrorResponse($message, $statusCode = 400) {
    sendJsonResponse(['ok' => false, 'msg' => $message], $statusCode);
}

/**
 * Send success response
 */
function sendSuccessResponse($data = null, $message = 'Success') {
    sendJsonResponse(['ok' => true, 'msg' => $message, 'data' => $data], 200);
}

/**
 * Validate input data
 */
function validateInput($data, $requiredFields = []) {
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            return false;
        }
    }
    return true;
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
