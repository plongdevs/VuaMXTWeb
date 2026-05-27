// Cloudflare Worker for Backend API
// This handles all backend logic while keeping it hidden from the frontend

// Environment variables (set in Cloudflare Workers dashboard)
const ADMIN_PASSWORD = 'your_admin_password_here'; // Change this!
const DB_PATH = '/database.sqlite'; // Will use Cloudflare D1 or KV

// Import D1 database (if using Cloudflare D1)
// export { D1Database } from '@cloudflare/workers-types';

// Simple in-memory storage for demo (replace with D1/KV in production)
let users = [];
let keys = [];
let tokens = [];
let sessions = new Map();

// Initialize with some demo data
function initializeData() {
    if (users.length === 0) {
        users = [
            { id: 1, username: 'user1', email: 'user1@example.com', password: 'hashed_password', is_pro: 0 },
            { id: 2, username: 'user2', email: 'user2@example.com', password: 'hashed_password', is_pro: 1 }
        ];
    }
    if (keys.length === 0) {
        keys = [
            { id: 1, key_code: 'PRO-ABCD-EFGH-IJKL-MNOP', is_used: 0 },
            { id: 2, key_code: 'PRO-QRST-UVWX-YZAB-CDEF', is_used: 1 }
        ];
    }
}

// CORS headers
const corsHeaders = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
    'Access-Control-Allow-Headers': 'Content-Type, Authorization',
};

export default {
    async fetch(request, env) {
        // Handle CORS preflight
        if (request.method === 'OPTIONS') {
            return new Response(null, { headers: corsHeaders });
        }

        const url = new URL(request.url);
        const path = url.pathname;

        try {
            // Initialize data
            initializeData();

            // Login endpoint
            if (path === '/api/login' && request.method === 'POST') {
                return handleLogin(request);
            }

            // Verify authentication for protected routes
            const authHeader = request.headers.get('Authorization');
            const token = authHeader?.replace('Bearer ', '');
            
            if (!token || !sessions.has(token)) {
                return new Response(JSON.stringify({ error: 'Unauthorized' }), {
                    status: 401,
                    headers: { ...corsHeaders, 'Content-Type': 'application/json' }
                });
            }

            // Users endpoints
            if (path === '/api/users' && request.method === 'GET') {
                return handleGetUsers();
            }
            if (path.match(/^\/api\/users\/\d+$/) && request.method === 'PUT') {
                const userId = parseInt(path.split('/').pop());
                return handleUpdateUser(request, userId);
            }
            if (path.match(/^\/api\/users\/\d+\/toggle-pro$/) && request.method === 'POST') {
                const userId = parseInt(path.split('/')[3]);
                return handleTogglePro(userId);
            }
            if (path === '/api/users/bulk-delete' && request.method === 'POST') {
                return handleBulkDeleteUsers(request);
            }

            // Keys endpoints
            if (path === '/api/keys' && request.method === 'GET') {
                return handleGetKeys();
            }
            if (path === '/api/keys/generate' && request.method === 'POST') {
                return handleGenerateKeys(request);
            }
            if (path === '/api/keys/bulk-delete' && request.method === 'POST') {
                return handleBulkDeleteKeys(request);
            }

            // Tokens endpoints
            if (path === '/api/tokens' && request.method === 'GET') {
                return handleGetTokens();
            }
            if (path === '/api/tokens/clear' && request.method === 'POST') {
                return handleClearTokens();
            }

            // Proxy endpoint for external API calls
            if (path === '/api/proxy' && request.method === 'POST') {
                return handleProxy(request);
            }

            // 404 for unknown routes
            return new Response(JSON.stringify({ error: 'Not found' }), {
                status: 404,
                headers: { ...corsHeaders, 'Content-Type': 'application/json' }
            });

        } catch (error) {
            console.error('Error:', error);
            return new Response(JSON.stringify({ error: 'Internal server error' }), {
                status: 500,
                headers: { ...corsHeaders, 'Content-Type': 'application/json' }
            });
        }
    }
};

// Handle login
async function handleLogin(request) {
    const { password } = await request.json();

    if (password === ADMIN_PASSWORD) {
        const token = generateToken();
        sessions.set(token, { logged: true, createdAt: Date.now() });
        
        return new Response(JSON.stringify({ success: true, token }), {
            headers: { ...corsHeaders, 'Content-Type': 'application/json' }
        });
    }

    return new Response(JSON.stringify({ success: false }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle get users
function handleGetUsers() {
    return new Response(JSON.stringify({ users }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle update user
async function handleUpdateUser(request, userId) {
    const { username, email } = await request.json();
    
    const userIndex = users.findIndex(u => u.id === userId);
    if (userIndex !== -1) {
        users[userIndex].username = username;
        users[userIndex].email = email;
        
        return new Response(JSON.stringify({ success: true }), {
            headers: { ...corsHeaders, 'Content-Type': 'application/json' }
        });
    }

    return new Response(JSON.stringify({ error: 'User not found' }), {
        status: 404,
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle toggle pro
function handleTogglePro(userId) {
    const userIndex = users.findIndex(u => u.id === userId);
    if (userIndex !== -1) {
        users[userIndex].is_pro = users[userIndex].is_pro ? 0 : 1;
        
        return new Response(JSON.stringify({ success: true }), {
            headers: { ...corsHeaders, 'Content-Type': 'application/json' }
        });
    }

    return new Response(JSON.stringify({ error: 'User not found' }), {
        status: 404,
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle bulk delete users
async function handleBulkDeleteUsers(request) {
    const { ids } = await request.json();
    
    users = users.filter(u => !ids.includes(u.id.toString()));
    
    return new Response(JSON.stringify({ success: true }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle get keys
function handleGetKeys() {
    return new Response(JSON.stringify({ keys }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle generate keys
async function handleGenerateKeys(request) {
    const { count } = await request.json();
    const generatedKeys = [];
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    
    for (let i = 0; i < count; i++) {
        const b = () => chars[Math.floor(Math.random() * chars.length)] + 
                       chars[Math.floor(Math.random() * chars.length)] + 
                       chars[Math.floor(Math.random() * chars.length)] + 
                       chars[Math.floor(Math.random() * chars.length)];
        const key = `PRO-${b()}-${b()}-${b()}-${b()}`;
        keys.push({ id: keys.length + 1, key_code: key, is_used: 0 });
        generatedKeys.push(key);
    }
    
    return new Response(JSON.stringify({ success: true, keys: generatedKeys }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle bulk delete keys
async function handleBulkDeleteKeys(request) {
    const { ids } = await request.json();
    
    keys = keys.filter(k => !ids.includes(k.id.toString()));
    
    return new Response(JSON.stringify({ success: true }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle get tokens
function handleGetTokens() {
    return new Response(JSON.stringify({ tokens }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle clear tokens
function handleClearTokens() {
    tokens = [];
    
    return new Response(JSON.stringify({ success: true }), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Handle proxy to external API
async function handleProxy(request) {
    const payload = await request.json();
    
    // This is where you would proxy to your actual backend API
    // For now, return mock data
    let response;
    
    switch (payload.action) {
        case 'access_to_jwt':
            response = {
                ok: true,
                data: {
                    decoded: {
                        nickname: 'TestUser',
                        lock_region: 'VN',
                        region: 'Asia'
                    }
                }
            };
            break;
        case 'check_platforms':
            response = {
                ok: true,
                data: {
                    main: 'Facebook',
                    platforms: ['Facebook', 'Google', 'Twitter']
                }
            };
            break;
        case 'check_email':
            response = {
                ok: true,
                data: {
                    email: 'test@example.com',
                    pending: false
                }
            };
            break;
        default:
            response = { ok: false, msg: 'Unknown action' };
    }
    
    return new Response(JSON.stringify(response), {
        headers: { ...corsHeaders, 'Content-Type': 'application/json' }
    });
}

// Generate random token
function generateToken() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let token = '';
    for (let i = 0; i < 32; i++) {
        token += chars[Math.floor(Math.random() * chars.length)];
    }
    return token;
}
