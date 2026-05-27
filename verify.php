<?php
// verify.php - Xử lý xác thực sau khi vượt link
require_once __DIR__ . '/config.php';

try {
    $db = initDatabase();
    
    $token = $_GET['token'] ?? '';
    
    if (!$token) {
        die("Thiếu thông tin xác thực!");
    }
    
    // Tự động xóa các token đã hết hạn
    $db->exec("DELETE FROM bypass_tokens WHERE created_at < datetime('now', '-" . BYPASS_TOKEN_LIFETIME . " seconds')");
    
    // Kiểm tra token
    $stmt = $db->prepare("SELECT * FROM bypass_tokens WHERE token = ? AND is_used = 0 AND created_at >= datetime('now', '-" . BYPASS_TOKEN_LIFETIME . " seconds')");
    $stmt->execute([$token]);
    $tData = $stmt->fetch();
    
    if (!$tData) {
        die("Link xác thực không hợp lệ, đã hết hạn (" . (BYPASS_TOKEN_LIFETIME / 60) . " phút) hoặc đã được sử dụng!");
    }
    
    $feature = $tData['feature'];
    
    // Đánh dấu token đã dùng
    $db->prepare("UPDATE bypass_tokens SET is_used = 1 WHERE id = ?")->execute([$tData['id']]);
    
    // Cộng lượt dùng cho user
    if ($feature === 'ban7day') {
        $db->prepare("UPDATE users SET ban7_count = MAX(0, ban7_count - 1) WHERE id = ?")->execute([$tData['uid']]);
    } else if ($feature === 'spamlog') {
        $db->prepare("UPDATE users SET spam_count = MAX(0, spam_count - 2) WHERE id = ?")->execute([$tData['uid']]);
    }
    
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Xác thực thành công</title><script>alert('Xác thực thành công! Bạn đã được cộng thêm lượt sử dụng.'); window.location.href='index.php';</script></head><body><p>Đang chuyển hướng...</p></body></html>";
    
} catch (PDOException $e) {
    die("Lỗi hệ thống: " . $e->getMessage());
}
