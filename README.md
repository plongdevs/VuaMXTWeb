# VuaMXT Admin Dashboard - Cloudflare Pages + Workers

Dashboard admin được chuyển đổi từ PHP sang HTML/CSS/JS để deploy lên Cloudflare Pages với backend xử lý bởi Cloudflare Workers.

## 📁 Cấu trúc dự án

```
Web/
├── index.html          # Frontend HTML
├── dashboard.css       # Magic UI Styles
├── app.js             # Frontend JavaScript
├── worker.js          # Cloudflare Worker (Backend API)
├── wrangler.toml      # Cloudflare Workers config
├── package.json       # Dependencies
└── README.md          # This file
```

## 🚀 Cách deploy lên Cloudflare Pages

### Bước 1: Deploy Cloudflare Worker (Backend)

1. Cài đặt Wrangler CLI:
```bash
npm install -g wrangler
```

2. Login vào Cloudflare:
```bash
wrangler login
```

3. Set mật khẩu admin (bí mật):
```bash
wrangler secret put ADMIN_PASSWORD
# Nhập mật khẩu admin của bạn
```

4. Deploy Worker:
```bash
wrangler deploy
```

5. Lấy URL của Worker (ví dụ: `https://vuamxt-admin.your-subdomain.workers.dev`)

### Bước 2: Cập nhật API URL trong app.js

Mở file `app.js` và thay đổi dòng 2:
```javascript
const API_URL = 'https://vuamxt-admin.your-subdomain.workers.dev/api';
```

### Bước 3: Deploy lên Cloudflare Pages

#### Cách 1: Sử dụng Git (Khuyên dùng)

1. Push code lên GitHub/GitLab
2. Vào Cloudflare Dashboard → Pages → Create a project
3. Connect repository và chọn folder `Web/`
4. Build settings: Không cần (static site)
5. Deploy

#### Cách 2: Sử dụng Wrangler CLI

```bash
cd Web/
wrangler pages deploy . --project-name=vuamxt-admin
```

### Bước 4: Cấu hình Routes (Optional)

Nếu muốn sử dụng custom domain hoặc routing, tạo file `_redirects`:
```
/api/* https://vuamxt-admin.your-subdomain.workers.dev/api/:splat 200
```

## 🔐 Cấu hình bảo mật

### Mật khẩu Admin

Set mật khẩu admin qua Wrangler secret:
```bash
wrangler secret put ADMIN_PASSWORD
```

Hoặc set trong Cloudflare Dashboard:
1. Vào Workers & Pages → Your Worker
2. Settings → Variables → Environment Variables
3. Add variable: `ADMIN_PASSWORD` = `your_password`

### Database (Optional)

Để lưu trữ dữ liệu lâu dài, sử dụng Cloudflare D1:

1. Tạo D1 database:
```bash
wrangler d1 create vuamxt_db
```

2. Cập nhật `wrangler.toml`:
```toml
[[d1_databases]]
binding = "DB"
database_name = "vuamxt_db"
database_id = "your_database_id"
```

3. Update worker.js để sử dụng D1 thay vì in-memory storage

## 📝 API Endpoints

### Authentication
- `POST /api/login` - Đăng nhập
- Headers: `{ "Content-Type": "application/json" }`
- Body: `{ "password": "your_password" }`
- Response: `{ "success": true, "token": "jwt_token" }`

### Users
- `GET /api/users` - Lấy danh sách users
- `PUT /api/users/:id` - Cập nhật user
- `POST /api/users/:id/toggle-pro` - Toggle PRO status
- `POST /api/users/bulk-delete` - Xóa nhiều users

### Keys
- `GET /api/keys` - Lấy danh sách keys
- `POST /api/keys/generate` - Tạo keys mới
- `POST /api/keys/bulk-delete` - Xóa nhiều keys

### Tokens
- `GET /api/tokens` - Lấy danh sách tokens
- `POST /api/tokens/clear` - Xóa tất cả tokens
- `POST /api/proxy` - Proxy đến external API

## 🎨 Tính năng

- ✅ Magic UI Design (gradient, animations, effects)
- ✅ Đăng nhập bảo mật với token
- ✅ Quản lý users (CRUD)
- ✅ Quản lý PRO keys
- ✅ Thu thập và quản lý tokens
- ✅ Responsive design
- ✅ Backend ẩn hoàn toàn (Cloudflare Workers)

## 🔧 Tùy chỉnh

### Thay đổi màu sắc
Mở `dashboard.css` và sửa CSS variables:
```css
:root {
  --primary: #6366f1;
  --gradient: linear-gradient(135deg, #6366f1, #a855f7, #ec4899);
  /* ... */
}
```

### Thay đổi hiệu ứng
Mở `dashboard.css` và tắt/bật các hiệu ứng:
- `.dot-pattern` - Nền chấm bi
- `.meteors-container` - Hiệu ứng meteors

## 📚 Tài liệu

- [Cloudflare Pages Docs](https://developers.cloudflare.com/pages/)
- [Cloudflare Workers Docs](https://developers.cloudflare.com/workers/)
- [Wrangler CLI Docs](https://developers.cloudflare.com/workers/wrangler/)

## ⚠️ Lưu ý quan trọng

1. **Backend hoàn toàn ẩn**: Tất cả logic backend nằm trong Cloudflare Worker, không thể xem từ frontend
2. **Environment Variables**: Không bao giờ commit mật khẩu hoặc sensitive data vào Git
3. **HTTPS**: Cloudflare tự động cung cấp HTTPS miễn phí
4. **Rate Limiting**: Có thể thêm rate limiting trong Worker để bảo vệ API

## 🐛 Troubleshooting

### Lỗi CORS
Nếu gặp lỗi CORS, đảm bảo worker.js có headers CORS đúng:
```javascript
const corsHeaders = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
    'Access-Control-Allow-Headers': 'Content-Type, Authorization',
};
```

### Lỗi 401 Unauthorized
Kiểm tra:
- Token có được lưu trong localStorage không
- Token có hợp lệ không
- Worker có nhận được Authorization header không

### Dữ liệu không lưu trữ
Worker hiện tại dùng in-memory storage. Để lưu trữ lâu dài:
- Sử dụng Cloudflare D1 cho database
- Sử dụng Cloudflare KV cho tokens
- Hoặc sử dụng external database (PostgreSQL, MySQL)

## 📄 License

Private - For internal use only
