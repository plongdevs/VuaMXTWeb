const API_BASE = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
    ? 'http://localhost:8000' 
    : 'https://vua-mxt-free-fire.vercel.app';

// DOM Elements
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const dashboard = document.getElementById('dashboard');
const loginBtn = document.getElementById('loginBtn');
const registerBtn = document.getElementById('registerBtn');
const logoutBtn = document.getElementById('logoutBtn');
const dashboardLink = document.getElementById('dashboardLink');
const toolModal = document.getElementById('toolModal');
const modalTitle = document.getElementById('modalTitle');
const modalBody = document.getElementById('modalBody');
const resultBox = document.getElementById('resultBox');
const closeModal = document.getElementById('closeModal');

// Check authentication on load
let token = localStorage.getItem('token');
if (token) {
    showDashboard();
}

// Event Listeners
loginBtn.addEventListener('click', () => {
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
});

registerBtn.addEventListener('click', () => {
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
});

document.getElementById('showRegister').addEventListener('click', (e) => {
    e.preventDefault();
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');
});

document.getElementById('showLogin').addEventListener('click', (e) => {
    e.preventDefault();
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
});

document.getElementById('loginFormElement').addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('loginUsername').value;
    const password = document.getElementById('loginPassword').value;

    try {
        const response = await fetch(`${API_BASE}/api/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (response.ok) {
            token = data.access_token;
            localStorage.setItem('token', token);
            showDashboard();
        } else {
            showResult(data.message || 'Login failed', 'error');
        }
    } catch (error) {
        showResult('Connection error', 'error');
    }
});

document.getElementById('registerFormElement').addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = document.getElementById('registerUsername').value;
    const email = document.getElementById('registerEmail').value;
    const password = document.getElementById('registerPassword').value;

    try {
        const response = await fetch(`${API_BASE}/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, email, password })
        });

        const data = await response.json();

        if (response.ok) {
            showResult('Registration successful! Please login.', 'success');
            setTimeout(() => {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
            }, 1500);
        } else {
            showResult(data.detail || 'Registration failed', 'error');
        }
    } catch (error) {
        showResult('Connection error', 'error');
    }
});

logoutBtn.addEventListener('click', () => {
    localStorage.removeItem('token');
    token = null;
    hideDashboard();
});

dashboardLink.addEventListener('click', (e) => {
    e.preventDefault();
    showDashboard();
});

closeModal.addEventListener('click', () => {
    toolModal.classList.remove('active');
});

toolModal.addEventListener('click', (e) => {
    if (e.target === toolModal) {
        toolModal.classList.remove('active');
    }
});

// Tool cards
document.querySelectorAll('.tool-card').forEach(card => {
    card.addEventListener('click', () => {
        const tool = card.dataset.tool;
        openToolModal(tool);
    });
});

function showDashboard() {
    loginForm.classList.add('hidden');
    registerForm.classList.add('hidden');
    dashboard.classList.add('active');
    loginBtn.classList.add('hidden');
    registerBtn.classList.add('hidden');
    logoutBtn.classList.remove('hidden');
    dashboardLink.classList.remove('hidden');
}

function hideDashboard() {
    dashboard.classList.remove('active');
    loginForm.classList.remove('hidden');
    loginBtn.classList.remove('hidden');
    registerBtn.classList.remove('hidden');
    logoutBtn.classList.add('hidden');
    dashboardLink.classList.add('hidden');
}

function showResult(message, type) {
    resultBox.textContent = message;
    resultBox.className = `result-box active ${type}`;
}

function openToolModal(tool) {
    modalTitle.textContent = getToolTitle(tool);
    modalBody.innerHTML = getToolForm(tool);
    resultBox.classList.remove('active');
    toolModal.classList.add('active');

    // Add event listener to the form
    const form = modalBody.querySelector('form');
    if (form) {
        form.addEventListener('submit', (e) => handleToolSubmit(e, tool));
    }
}

function getToolTitle(tool) {
    const titles = {
        'add-email': 'Add Recovery Email',
        'check-email': 'Check Recovery Email',
        'check-platforms': 'Check Linked Platforms',
        'cancel-email': 'Cancel Recovery Email',
        'revoke-token': 'Revoke Access Token',
        'eat-to-access': 'EAT to Access Token',
        'eat-to-jwt': 'EAT to JWT',
        'access-to-jwt': 'Access to JWT',
        'guest-to-jwt': 'Guest to JWT'
    };
    return titles[tool] || tool;
}

function getToolForm(tool) {
    const forms = {
        'add-email': `
            <form>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Access Token</label>
                    <input type="text" name="access_token" required>
                </div>
                <div class="form-group">
                    <label>OTP</label>
                    <input type="text" name="otp" required>
                </div>
                <div class="form-group">
                    <label>Security Code (6 digits)</label>
                    <input type="text" name="security_code" pattern="[0-9]{6}" required>
                </div>
                <button type="submit" class="submit-btn">Add Email</button>
            </form>
        `,
        'check-email': `
            <form>
                <div class="form-group">
                    <label>Access Token</label>
                    <input type="text" name="access_token" required>
                </div>
                <button type="submit" class="submit-btn">Check Email</button>
            </form>
        `,
        'check-platforms': `
            <form>
                <div class="form-group">
                    <label>Access Token</label>
                    <input type="text" name="access_token" required>
                </div>
                <button type="submit" class="submit-btn">Check Platforms</button>
            </form>
        `,
        'cancel-email': `
            <form>
                <div class="form-group">
                    <label>Access Token</label>
                    <input type="text" name="access_token" required>
                </div>
                <button type="submit" class="submit-btn">Cancel Request</button>
            </form>
        `,
        'revoke-token': `
            <form>
                <div class="form-group">
                    <label>Access Token</label>
                    <input type="text" name="access_token" required>
                </div>
                <button type="submit" class="submit-btn">Revoke Token</button>
            </form>
        `,
        'eat-to-access': `
            <form>
                <div class="form-group">
                    <label>EAT Token / Link</label>
                    <input type="text" name="eat_token" required>
                </div>
                <button type="submit" class="submit-btn">Convert to Access</button>
            </form>
        `,
        'eat-to-jwt': `
            <form>
                <div class="form-group">
                    <label>EAT Token / Link</label>
                    <input type="text" name="eat_token" required>
                </div>
                <button type="submit" class="submit-btn">Convert to JWT</button>
            </form>
        `,
        'access-to-jwt': `
            <form>
                <div class="form-group">
                    <label>Access Token</label>
                    <input type="text" name="access_token" required>
                </div>
                <button type="submit" class="submit-btn">Convert to JWT</button>
            </form>
        `,
        'guest-to-jwt': `
            <form>
                <div class="form-group">
                    <label>UID</label>
                    <input type="text" name="uid" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">Convert to JWT</button>
            </form>
        `
    };
    return forms[tool] || '<p>Tool form not found</p>';
}

async function handleToolSubmit(e, tool) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    const endpoints = {
        'add-email': '/api/add-recovery-email',
        'check-email': '/api/check-recovery-email',
        'check-platforms': '/api/check-platforms',
        'cancel-email': '/api/cancel-recovery-email',
        'revoke-token': '/api/revoke-token',
        'eat-to-access': '/api/eat-to-access',
        'eat-to-jwt': '/api/eat-to-jwt',
        'access-to-jwt': '/api/access-to-jwt',
        'guest-to-jwt': '/api/guest-to-jwt'
    };

    const endpoint = endpoints[tool];
    if (!endpoint) return;

    try {
        const headers = {
            'Content-Type': 'application/json'
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        const response = await fetch(`${API_BASE}${endpoint}`, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showResult(JSON.stringify(result, null, 2), 'success');
        } else {
            showResult(result.message || JSON.stringify(result, null, 2), 'error');
        }
    } catch (error) {
        showResult('Connection error: ' + error.message, 'error');
    }
}
