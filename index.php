<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>VuaMXT</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="style.css"/>
</head>
<body>

<nav class="gt-nav">
  <div class="gt-logo">
    <div class="gt-logo-dot"></div>
    VuaMXT
  </div>
  <span class="gt-ver">v1.1</span>
</nav>

<div class="gt-wrap">

  <div class="token-card">
    <div class="token-card-head" id="authHeader">
      <div class="token-card-head-ico" id="authIco"><i class="bi bi-shield-lock-fill"></i></div>
      <div>
        <div class="token-card-head-title" id="authTitle">Hệ Thống Đăng Nhập</div>
        <div class="token-card-head-sub" id="authSub">Vui lòng đăng nhập để mở khóa các chức năng</div>
      </div>
    </div>
    <div class="token-card-body">
      <div id="authFormSection">
        <div class="row g-3 mb-4">
          <div class="col-12">
            <label class="gt-label">Tên đăng nhập</label>
            <input class="gt-input" id="authUsername" placeholder="Nhập tên tài khoản..."/>
          </div>
          <div class="col-12" id="emailInputRow" style="display: none;">
            <label class="gt-label">Địa chỉ Email</label>
            <input class="gt-input" id="authEmail" type="email" placeholder="Nhập email của bạn..."/>
          </div>
          <div class="col-12">
            <label class="gt-label">Mật khẩu</label>
            <input class="gt-input" id="authPassword" type="password" placeholder="••••••••"/>
          </div>
        </div>
        <div class="btn-row" id="authActionButtons">
          <button class="gt-btn gt-btn-primary" onclick="submitAuth('login')"><i class="bi bi-box-arrow-in-right"></i> Đăng Nhập</button>
          <button class="gt-btn gt-btn-ghost" onclick="toggleAuthMode(true)">Chưa có tài khoản? Đăng ký</button>
        </div>
        <div class="btn-row" id="authRegisterButtons" style="display: none;">
          <button class="gt-btn gt-btn-success" onclick="submitAuth('register')"><i class="bi bi-person-plus-fill"></i> Xác Nhận Đăng Ký</button>
          <button class="gt-btn gt-btn-ghost" onclick="toggleAuthMode(false)">Quay lại Đăng nhập</button>
        </div>
      </div>

      <div id="userProfileSection" style="display: none;">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div style="font-size: 15px;">
            Tài khoản: <strong class="text-white" id="profileUser" style="font-family: var(--mono);">--</strong>
            <span id="profileBadge" class="gt-tag warn-tag ms-2">FREE</span>
          </div>
          <button class="gt-btn gt-btn-danger gt-btn-sm" onclick="submitLogout()"><i class="bi bi-power"></i> Đăng xuất</button>
        </div>
        
        <div id="keyActivationBlock" style="border-top: 1px dashed var(--bd2); padding-top: 20px;">
          <label class="gt-label">Nâng cấp gói PRO VIP</label>
          <div class="d-flex gap-2 mb-2">
            <input class="gt-input" id="proLicenseKey" placeholder="Nhập mã bản quyền (Key Pro)..."/>
            <button class="gt-btn gt-btn-primary" onclick="submitActivateKey()"><i class="bi bi-lightning-charge-fill"></i></button>
          </div>
          <div style="font-size: 11px; color: var(--t2); margin-bottom: 8px;">Mua key Pro để mở khóa đếm ngược tối đa 15 ngày và vô hạn lượt chạy.</div>
          <div style="font-size: 11px; color: var(--t2); font-family: var(--mono);">Ib Telegram: <a href="https://t.me/plongdeveloper" target="_blank" style="color: var(--acc); text-decoration: none; font-weight: bold;">@plongdeveloper</a> để mua key</div>
        </div>
      </div>
    </div>
  </div>

  <div id="mainAppContent" class="disabled">

    <div class="token-card">
      <div class="token-card-head">
        <div class="token-card-head-ico"><i class="bi bi-key-fill"></i></div>
        <div>
          <div class="token-card-head-title">Lấy Access Token</div>
          <div class="token-card-head-sub">Đăng nhập bằng tài khoản liên kết</div>
        </div>
      </div>
      <div class="token-card-body">

        <div class="social-grid">
          <a class="social-btn social-fb" href="https://auth.garena.com/universal/oauth?platform=3&response_type=code&locale=en-SG&client_id=100067&redirect_uri=https://api.ff.garena.co.id/auth/auth/callback_n?site=https://api-ticket.ff.gameid.garena.co.id/oauth/callback_redirect/" target="_blank" rel="noopener">
            <div class="social-ico"><i class="bi bi-facebook"></i></div>
            <span>Facebook</span>
          </a>
          <a class="social-btn social-gg" href="https://auth.garena.com/universal/oauth?platform=8&response_type=code&locale=en-SG&client_id=100067&redirect_uri=https://api.ff.garena.co.id/auth/auth/callback_n?site=https://api-ticket.ff.gameid.garena.co.id/oauth/callback_redirect/" target="_blank" rel="noopener">
            <div class="social-ico">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            </div>
            <span>Google</span>
          </a>
          <a class="social-btn social-vk" href="https://auth.garena.com/universal/oauth?platform=5&response_type=code&locale=en-SG&client_id=100067&redirect_uri=https://api.ff.garena.co.id/auth/auth/callback_n?site=https://api-ticket.ff.gameid.garena.co.id/oauth/callback_redirect/" target="_blank" rel="noopener">
            <div class="social-ico">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="#0077FF"><path d="M12.785 16.241s.288-.032.436-.194c.136-.148.132-.427.132-.427s-.02-1.304.585-1.496c.598-.19 1.365 1.26 2.179 1.815.615.418 1.082.326 1.082.326l2.172-.03s1.135-.071.597-1.099c-.044-.078-.312-.66-1.606-1.865-1.354-1.26-1.173-1.057.458-3.237.994-1.32 1.39-2.125 1.266-2.47-.117-.33-.85-.243-.85-.243l-2.444.015s-.181-.025-.315.056c-.132.079-.216.264-.216.264s-.384.986-.895 1.823c-1.077 1.79-1.508 1.884-1.685 1.773-.409-.266-.307-1.065-.307-1.635 0-1.776.267-2.518-.521-2.715-.26-.064-.452-.105-1.12-.113-.857-.008-1.582.003-1.993.203-.273.13-.485.421-.356.437.158.021.519.098.71.361.246.341.237 1.107.237 1.107s.142 2.092-.33 2.352c-.325.177-.77-.184-1.726-1.834-.49-.826-.86-1.739-.86-1.739s-.072-.176-.202-.272a1.04 1.04 0 0 0-.406-.144l-2.32.015s-.348.01-.476.163c-.113.136-.01.42-.01.42s1.816 4.3 3.872 6.47c1.886 1.994 4.028 1.863 4.028 1.863h.972z"/></svg>
            </div>
            <span>VK</span>
          </a>
          <a class="social-btn social-tw" href="https://auth.garena.com/universal/oauth?platform=11&response_type=code&locale=en-SG&client_id=100067&redirect_uri=https://api.ff.garena.co.id/auth/auth/callback_n?site=https://api-ticket.ff.gameid.garena.co.id/oauth/callback_redirect/" target="_blank" rel="noopener">
            <div class="social-ico">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </div>
            <span>Twitter</span>
          </a>
          <a class="social-btn social-ap" href="https://auth.garena.com/universal/oauth?platform=10&response_type=code&locale=en-SG&client_id=100067&redirect_uri=https://api.ff.garena.co.id/auth/auth/callback_n?site=https://api-ticket.ff.gameid.garena.co.id/oauth/callback_redirect/" target="_blank" rel="noopener">
            <div class="social-ico">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.7 9.05 7.4c1.32.07 2.23.72 3 .77.97-.19 1.9-.89 3.06-.96 1.37-.09 2.68.58 3.49 1.66-3.15 1.97-2.51 5.96.47 7.32-.58 1.52-1.31 3.01-2.02 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
            </div>
            <span>Apple</span>
          </a>
        </div> <div class="tutorial-wrap">
          <div class="tutorial-title"><i class="bi bi-play-circle-fill"></i> Hướng dẫn lấy Access Token</div>
          <div class="tutorial-video-box">
            <video controls muted playsinline preload="metadata">
              <source src="tutorial.mp4" type="video/mp4">
              Trình duyệt của bạn không hỗ trợ thẻ video.
            </video>
          </div>
        </div>

      </div> </div>
<div id="noticeBox" style="background:#fff3cd;border:1px solid #ffc107;border-radius:8px;padding:10px 14px;display:flex;align-items:flex-start;gap:10px;font-size:12px;color:#664d03;margin-bottom:10px">
  <i class="bi bi-exclamation-triangle-fill" style="font-size:14px;flex-shrink:0;margin-top:1px;color:#e8a020"></i>
  <span>Trang web này được tạo ra cho mục đích tra cứu, không cổ xúy cho các hành động scam, đánh cắp tài khoản. Nếu bạn sử dụng với mục đích bất hợp pháp, chúng tôi sẽ không chịu trách nhiệm về hậu quả!</span>
  <button onclick="document.getElementById('noticeBox').style.display='none'" style="margin-left:auto;background:none;border:none;font-size:18px;cursor:pointer;color:#664d03;line-height:1;flex-shrink:0;padding:0">×</button>
</div>
    <div class="gt-acc">

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-em">
          <span class="acc-ico"><i class="bi bi-envelope"></i></span>
          <span class="acc-lbl">Check Email Khôi Phục <span class="acc-sub">Xem email khôi phục nào đang gắn vào tài khoản</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-em" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div class="mb-3"><label class="gt-label">Access Token</label><input class="gt-input" id="em-at" type="password" placeholder="Dán access token vào đây..."/></div>
            <button class="gt-btn gt-btn-primary" onclick="doCheckEmail()"><span id="em-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-search"></i> Kiểm Tra</button>
            <div class="gt-res" id="em-res"><button class="gt-copy" onclick="cpRes('em-res')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-pl">
          <span class="acc-ico"><i class="bi bi-link-45deg"></i></span>
          <span class="acc-lbl">Kiểm Tra Liên Kết<span class="acc-sub">Kiểm tra tất cả liên kết có trong tài khoản</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-pl" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div class="mb-3"><label class="gt-label">Access Token</label><input class="gt-input" id="pl-at" type="password" placeholder="Dán access token vào đây..."/></div>
            <button class="gt-btn gt-btn-primary" onclick="doCheckPlatforms()"><span id="pl-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-search"></i> Kiểm Tra</button>
            <div id="pl-tbl"></div>
          </div>
        </div>
      </div>

    </div> 

    <div class="gt-acc">

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-eat">
          <span class="acc-ico"><i class="bi bi-arrow-left-right"></i></span>
          <span class="acc-lbl">EAT → Access Token / JWT <span class="acc-sub">Chuyển Eat Token hoặc link Eat Token thành Access hoặc JWT Token</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-eat" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div class="mb-3"><label class="gt-label">Nhập EAT Token hoặc Link</label><textarea class="gt-input" id="eat-val" rows="2" placeholder="Dán Eat token hoặc full URL vào đây..."></textarea></div>
            <div class="btn-row">
              <button class="gt-btn gt-btn-primary" onclick="doEatToAccess()"><span id="eat-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-key"></i> Lấy Access Token</button>
              <button class="gt-btn gt-btn-ghost" onclick="doEatToJwt()"><i class="bi bi-code-slash"></i> Lấy JWT</button>
            </div>
            <div class="gt-res" id="eat-res"><button class="gt-copy" onclick="cpRes('eat-res')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-atj">
          <span class="acc-ico"><i class="bi bi-code-square"></i></span>
          <span class="acc-lbl">Access Token → JWT Token<span class="acc-sub">Chuyển đổi Access Token thành JWT Token</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-atj" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div class="mb-3"><label class="gt-label">Access Token</label><input class="gt-input" id="atj-at" type="password" placeholder="Dán access token vào đây..."/></div>
            <button class="gt-btn gt-btn-primary" onclick="doAccessToJwt()"><span id="atj-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-arrow-right-circle"></i> Lấy JWT Token</button>
            <div class="gt-res" id="atj-res"><button class="gt-copy" onclick="cpRes('atj-res')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-gu">
          <span class="acc-ico"><i class="bi bi-person-badge"></i></span>
          <span class="acc-lbl">Guest → JWT <span class="acc-sub">Chuyển tài khoản khách thành jwt token</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-gu" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div class="row g-3 mb-3">
              <div class="col-sm-6"><label class="gt-label">UID</label><input class="gt-input" id="gu-uid" placeholder="Guest UID..."/></div>
              <div class="col-sm-6"><label class="gt-label">Password</label><input class="gt-input" id="gu-pw" type="password" placeholder="Password..."/></div>
            </div>
            <button class="gt-btn gt-btn-primary" onclick="doGuestJwt()"><span id="gu-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-box-arrow-in-right"></i> Lấy JWT</button>
            <div class="gt-res" id="gu-res"><button class="gt-copy" onclick="cpRes('gu-res')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-rv">
          <span class="acc-ico red"><i class="bi bi-x-circle"></i></span>
          <span class="acc-lbl">Vô hiệu hoá Access Token <span class="acc-sub">Vô hiệu hóa Access Token ngay lập tức</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-rv" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <p style="font-size:12px;color:var(--t2);font-family:var(--mono);margin-bottom:16px;line-height:1.7">⚠️ Sau khi vô hiệu hoá token này bạn cần đăng nhập lại và lấy token mới để sử dụng các tính năng khác.</p>
            <div class="mb-3"><label class="gt-label">Access Token</label><input class="gt-input" id="rv-at" type="password" placeholder="Dán access token vào đây..."/></div>
            <button class="gt-btn gt-btn-danger" onclick="doRevoke()"><span id="rv-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-x-circle"></i> Vô hiệu hoá</button>
            <div class="gt-res" id="rv-res"><button class="gt-copy" onclick="cpRes('rv-res')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-lb">
          <span class="acc-ico purp"><i class="bi bi-pencil-square"></i></span>
          <span class="acc-lbl">Tiểu sử dài <span class="acc-sub">Đặt tiểu sử dài cho tài khoản của bạn (Hỗ trợ kí tự đặc biệt)</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-lb" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div class="mb-3">
              <label class="gt-label">Phương thức đăng nhập</label>
              <select class="gt-input" id="lb-method" onchange="toggleLBFields()">
                <option value="at">Access Token</option>
                <option value="jwt">JWT Token</option>
                <option value="guest">Guest (UID/Pass)</option>
              </select>
            </div>
            
            <div id="lb-field-atjwt">
              <div class="mb-3"><label class="gt-label" id="lb-tok-label">Access Token</label><input class="gt-input" id="lb-tok" type="password" placeholder="Dán access token vào đây..."/></div>
            </div>
            
            <div id="lb-field-guest" style="display:none">
              <div class="row g-3 mb-3">
                <div class="col-sm-6"><label class="gt-label">UID Guest</label><input class="gt-input" id="lb-uid" placeholder="UID..."/></div>
                <div class="col-sm-6"><label class="gt-label">Password</label><input class="gt-input" id="lb-pass" type="password" placeholder="Pass..."/></div>
              </div>
            </div>

            <div class="mb-3"><label class="gt-label">Tiểu sử</label><textarea class="gt-input" id="lb-bio" rows="3" placeholder="Nhập tiểu sử mới..."></textarea></div>
            <button class="gt-btn gt-btn-primary" onclick="doLongBio()"><span id="lb-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-pencil-square"></i> Cập nhật Bio</button>
            <div class="gt-res" id="lb-res"><button class="gt-copy" onclick="cpRes('lb-res')">copy</button><span></span></div>
          </div>
        </div>
      </div>

    </div> 
    
    <div class="gt-acc">

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-ae" onclick="resetEmailState('ae')">
          <span class="acc-ico grn"><i class="bi bi-plus-circle"></i></span>
          <span class="acc-lbl">Thêm Email Xác Thực<span class="acc-sub">Gắn email xác thực vào tài khoản</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-ae" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div id="ae-step1">
              <div class="step-row"><span class="step-n">1</span><span class="step-lbl">Gửi OTP tới email muốn thêm</span></div>
              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="gt-label">Access Token</label><input class="gt-input" id="ae-at" type="password" placeholder="Dán access token..."/></div>
                <div class="col-md-6"><label class="gt-label">Email mới</label><input class="gt-input" id="ae-email" type="email" placeholder="new@gmail.com"/></div>
              </div>
              <button class="gt-btn gt-btn-primary gt-btn-sm" onclick="doSendOtp()"><span id="ae-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-send"></i> Gửi OTP</button>
            </div>

            <div id="ae-step2" style="display:none">
              <hr class="gt-hr"/>
              <div class="step-row"><span class="step-n">2</span><span class="step-lbl">Nhập mã OTP và đặt mật khẩu bảo mật</span></div>
              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="gt-label">Mã OTP</label><input class="gt-input" id="ae-otp" placeholder="6 chữ số..."/></div>
                <div class="col-md-6"><label class="gt-label">Mã bảo mật mới (6 số)</label><input class="gt-input" id="ae-sp2" type="password" placeholder="######"/></div>
              </div>
              <button class="gt-btn gt-btn-primary gt-btn-sm" onclick="doVerifyOtp()"><span id="vo-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-check-circle"></i> Xác Thực OTP</button>
            </div>

            <div id="ae-step3" style="display:none">
              <hr class="gt-hr"/>
              <div class="step-row"><span class="step-n">3</span><span class="step-lbl">Xác nhận gắn email</span></div>
              <p class="text-muted mb-3" style="font-size:12px">Hệ thống đã nhận được Verifier Token. Nhấn nút bên dưới để hoàn tất.</p>
              <button class="gt-btn gt-btn-success" onclick="doCreateBind()"><span id="cb-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-plus-lg"></i> Hoàn Tất Thêm Email</button>
            </div>
            
            <div class="gt-res" id="ae-res-box"><button class="gt-copy" onclick="cpRes('ae-res-box')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-re" onclick="resetEmailState('re')">
          <span class="acc-ico red"><i class="bi bi-dash-circle"></i></span>
          <span class="acc-lbl">Gỡ Email Khôi Phục<span class="acc-sub">Xóa Email Khôi Phục Ra Khỏi Tài Khoản</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-re" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div id="re-step1">
              <div class="step-row"><span class="step-n">1</span><span class="step-lbl">Thông tin & Xác minh</span></div>
              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="gt-label">Access Token</label><input class="gt-input" id="re-at" type="password" placeholder="Dán access token..."/></div>
                <div class="col-md-6"><label class="gt-label">Email hiện tại</label><input class="gt-input" id="re-email" type="email" placeholder="current@gmail.com"/></div>
              </div>
              <div class="mb-3">
                <label class="gt-label">Chọn cách xác minh</label>
                <div class="btn-row">
                  <button class="gt-btn gt-btn-ghost gt-btn-sm" onclick="setVerifyMethod('re', 'otp')"><i class="bi bi-envelope-at"></i> Dùng mã OTP</button>
                  <button class="gt-btn gt-btn-ghost gt-btn-sm" onclick="setVerifyMethod('re', 'sp')"><i class="bi bi-shield-lock"></i> Dùng mã bảo mật</button>
                </div>
              </div>

              <div id="re-verify-input" style="display:none" class="mb-3">
                <div id="re-otp-box" style="display:none">
                  <label class="gt-label">Mã OTP (Gửi tới email cũ)</label>
                  <div style="display:flex; gap:10px">
                    <input class="gt-input" id="re-otp" placeholder="Nhập OTP..."/>
                    <button class="gt-btn gt-btn-primary gt-btn-sm" onclick="doSendOtpForVerify('re')"><i class="bi bi-send"></i> Gửi</button>
                  </div>
                </div>
                <div id="re-sp-box" style="display:none">
                  <label class="gt-label">Mã bảo mật (6 số)</label>
                  <input class="gt-input" id="re-sp" type="password" placeholder="######"/>
                </div>
                <button class="gt-btn gt-btn-primary mt-3 w-100" onclick="doVerifyIdentity('re', 'remove')"><span id="ri-sp" class="spin" style="display:none; margin-right: 8px;"></span>Xác Minh Danh Tính</button>
              </div>
            </div>

            <div id="re-step2" style="display:none">
              <hr class="gt-hr"/>
              <div class="step-row"><span class="step-n">2</span><span class="step-lbl">Xác nhận gỡ email</span></div>
              <p class="text-muted mb-3" style="font-size:12px">Đã nhận được Identity Token. Nhấn nút bên dưới để hoàn tất.</p>
              <button class="gt-btn gt-btn-danger" onclick="doCreateUnbind()"><span id="cu-sp" class="spin" style="display:none; margin-right: 8px;"></span> Hoàn Tất Gỡ Email</button>
            </div>

            <div class="gt-res" id="re-res-box"><button class="gt-copy" onclick="cpRes('re-res-box')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-ce" onclick="resetEmailState('ce')">
          <span class="acc-ico blu"><i class="bi bi-arrow-repeat"></i></span>
          <span class="acc-lbl">Đổi Email Khôi Phục <span class="acc-sub">Thay email khôi phục cũ bằng email mới</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-ce" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div id="ce-step1">
              <div class="step-row"><span class="step-n">1</span><span class="step-lbl">Xác minh email cũ</span></div>
              <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="gt-label">Access Token</label><input class="gt-input" id="ce-at" type="password" placeholder="Dán access token..."/></div>
                <div class="col-md-6"><label class="gt-label">Email hiện tại</label><input class="gt-input" id="ce-email" type="email" placeholder="old@gmail.com"/></div>
              </div>
              <div class="mb-3">
                <label class="gt-label">Xác minh email cũ bằng</label>
                <div class="btn-row">
                  <button class="gt-btn gt-btn-ghost gt-btn-sm" onclick="setVerifyMethod('ce', 'otp')"><i class="bi bi-envelope-at"></i> Dùng mã OTP</button>
                  <button class="gt-btn gt-btn-ghost gt-btn-sm" onclick="setVerifyMethod('ce', 'sp')"><i class="bi bi-shield-lock"></i> Dùng mã bảo mật</button>
                </div>
              </div>

              <div id="ce-verify-input" style="display:none" class="mb-3">
                <div id="ce-otp-box" style="display:none">
                  <label class="gt-label">Mã OTP (Gửi tới email cũ)</label>
                  <div style="display:flex; gap:10px">
                    <input class="gt-input" id="ce-otp" placeholder="Nhập OTP..."/>
                    <button class="gt-btn gt-btn-primary gt-btn-sm" onclick="doSendOtpForVerify('ce')"><i class="bi bi-send"></i> Gửi</button>
                  </div>
                </div>
                <div id="ce-sp-box" style="display:none">
                  <label class="gt-label">Mã bảo mật (6 số)</label>
                  <input class="gt-input" id="ce-sp2" type="password" placeholder="######"/>
                </div>
                <button class="gt-btn gt-btn-primary mt-3 w-100" onclick="doVerifyIdentity('ce', 'change')"><span id="ci-sp" class="spin" style="display:none; margin-right: 8px;"></span>Xác Minh Email Cũ</button>
              </div>
            </div>

            <div id="ce-step2" style="display:none">
              <hr class="gt-hr"/>
              <div class="step-row"><span class="step-n">2</span><span class="step-lbl">Gửi OTP tới email mới</span></div>
              <div class="mb-3"><label class="gt-label">Địa chỉ email mới</label><input class="gt-input" id="ce-newemail" type="email" placeholder="new@gmail.com"/></div>
              <button class="gt-btn gt-btn-primary gt-btn-sm" onclick="doSendOtpChange()"><span id="cos-sp" class="spin" style="display:none; margin-right: 8px;"></span> Gửi OTP Đến Email Mới</button>
            </div>

            <div id="ce-step3" style="display:none">
              <hr class="gt-hr"/>
              <div class="step-row"><span class="step-n">3</span><span class="step-lbl">Xác thực & Xác nhận đổi</span></div>
              <div class="mb-3"><label class="gt-label">Mã OTP (từ email mới)</label><input class="gt-input" id="ce-newotp" placeholder="Nhập mã 6 số..."/></div>
              <div class="btn-row">
                <button class="gt-btn gt-btn-ghost gt-btn-sm" onclick="doVerifyOtpChange()"><span id="cov-sp" class="spin" style="display:none; margin-right: 8px;"></span> Xác Thực OTP</button>
                <button class="gt-btn gt-btn-success gt-btn-sm" id="btn-final-ce" style="display:none" onclick="doCreateRebind()"><span id="cr-sp" class="spin" style="display:none; margin-right: 8px;"></span> Hoàn Tất Đổi Email</button>
              </div>
            </div>

            <div class="gt-res" id="ce-res-box"><button class="gt-copy" onclick="cpRes('ce-res-box')">copy</button><span></span></div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-ca">
          <span class="acc-ico"><i class="bi bi-x-square"></i></span>
          <span class="acc-lbl">Hủy Quá Trình Thêm Email Khôi Phục <span class="acc-sub">Hủy quá trình thêm email khôi phục khi chưa hết 15 ngày</span></span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-ca" class="accordion-collapse collapse">
          <div class="gt-acc-body">
            <div class="mb-3"><label class="gt-label">Access Token</label><input class="gt-input" id="ca-at" type="password" placeholder="Dán access token vào đây..."/></div>
            <button class="gt-btn gt-btn-danger" onclick="doCancelEmail()"><span id="ca-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-x-lg"></i> Hủy Email</button>
            <div class="gt-res" id="ca-res"><button class="gt-copy" onclick="cpRes('ca-res')">copy</button><span></span></div>
          </div>
        </div>
      </div>

    </div>

    <div class="gt-acc">

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-b7">
          <span class="acc-ico red"><i class="bi bi-hammer"></i></span>
          <span class="acc-lbl">
            Ban 7 Ngày 
            <span class="acc-sub">Khoá tài khoản 7 Ngày</span>
            <span class="acc-sub mt-1"><b style="color:var(--red)" id="ban7QuotaText">Đã dùng 0 / 1</b></span>
          </span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-b7" class="accordion-collapse collapse">
          <div class="gt-acc-body" id="b7-main-body">
            <p style="font-size:12px;color:var(--t2);font-family:var(--mono);margin-bottom:16px;line-height:1.7">⚠️ Sau khi ban tàu khoản này 7 ngày mà email xác thực vẫn chưa hết 15 ngày thì sẽ bị hủy!.</p>
            <div class="mb-3"><label class="gt-label">Access Token</label><input class="gt-input" id="b7-at" type="password" placeholder="Dán access token vào đây..."/></div>
            <button class="gt-btn gt-btn-danger" onclick="doBan7()"><span id="b7-sp" class="spin" style="display:none; margin-right: 8px;"></span><i class="bi bi-hammer"></i> Thực Thi Ban</button>
            <div class="gt-res" id="b7-res"><button class="gt-copy" onclick="cpRes('b7-res')">copy</button><span></span></div>
          </div>
          <div class="gt-acc-body" id="b7-limit-body" style="display:none">
            <div class="text-center p-3">
              <p style="color:var(--t2); margin-bottom: 20px;">Bạn đã hết lượt dùng thử Ban 7 Ngày vui lòng nâng gói Pro hoặc vượt link để nhận thêm 1 lần sử dụng miễn phí</p>
              <div class="d-flex gap-2 justify-content-center">
                <button class="gt-btn gt-btn-primary" onclick="window.scrollTo({top:0, behavior:'smooth'})">Mua Pro</button>
                <button class="gt-btn gt-btn-success" onclick="getBypassLink('ban7day')">Vượt link</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="gt-acc-item">
        <h2><button class="gt-acc-btn collapsed" data-bs-toggle="collapse" data-bs-target="#ac-gd">
          <span class="acc-ico" style="background:rgba(115, 148, 236, 0.2); color: var(--acc);"><i class="bi bi-shield-fill-exclamation"></i></span>
          <span class="acc-lbl">
            Spam Log 
            <span class="acc-sub">Liên tục log acc để đá người khác ra khỏi tài khoản</span>
            <span class="acc-sub mt-1"><b style="color:var(--acc)" id="spamQuotaText">Đã dùng 0 / 2</b></span>
          </span>
          <i class="bi bi-chevron-down acc-chevron"></i>
        </button></h2>
        <div id="ac-gd" class="accordion-collapse collapse">
          <div class="gt-acc-body" id="spam-main-body">
            <div class="mb-3"><label class="gt-label">Access Token</label><input class="gt-input" id="sp-at" type="password" placeholder="Dán access token vào đây..."/></div>
            <div class="mb-3"><label class="gt-label">Tốc độ gửi</label>
              <select class="gt-input" id="sp-iv">
                <option value="300">300ms — Mạnh nhất</option>
                <option value="500" selected>500ms — Khuyến nghị</option>
                <option value="1000">1000ms — Nhẹ</option>
                <option value="2000">2000ms — Tối thiểu</option>
              </select>
            </div>

            <div class="mb-4" style="border: 1px solid var(--bd); padding: 16px; border-radius: 12px; background: rgba(0,0,0,0.2);">
              <label class="gt-label" style="color: var(--acc);"><i class="bi bi-hourglass-split"></i> Đếm ngược thời gian chạy (Max 15 ngày)</label>
              <div class="row g-2 mt-2">
                <div class="col-4">
                  <input class="gt-input text-center" id="tDays" type="number" min="0" max="15" value="0" placeholder="0"/>
                  <div style="font-size:10px;color:var(--t3);text-align:center;margin-top:6px;font-weight:700;">NGÀY</div>
                </div>
                <div class="col-4">
                  <input class="gt-input text-center" id="tHours" type="number" min="0" max="23" value="1" placeholder="1"/>
                  <div style="font-size:10px;color:var(--t3);text-align:center;margin-top:6px;font-weight:700;">GIỜ</div>
                </div>
                <div class="col-4">
                  <input class="gt-input text-center" id="tMins" type="number" min="0" max="59" value="0" placeholder="0"/>
                  <div style="font-size:10px;color:var(--t3);text-align:center;margin-top:6px;font-weight:700;">PHÚT</div>
                </div>
              </div>
            </div>

            <hr class="gt-hr"/>
            <div class="guard-ring-wrap">
              <div class="guard-ring" id="gRing">
                <div class="guard-ring-inner">
                  <div class="guard-status" id="gStatus">IDLE</div>
                  <div class="guard-ip" id="gIp">--</div>
                </div>
              </div>
            </div>
            <div class="guard-stats">
              <div class="guard-stat"><div class="guard-stat-val" id="gSent">0</div><div class="guard-stat-lbl">Đã Gửi</div></div>
              <div class="guard-stat"><div class="guard-stat-val" id="gOk" style="color:var(--grn)">0</div><div class="guard-stat-lbl">Thành Công</div></div>
              <div class="guard-stat"><div class="guard-stat-val" id="gFail" style="color:var(--red)">0</div><div class="guard-stat-lbl">Thất Bại</div></div>
            </div>
            <div class="btn-row justify-content-center">
              <button class="gt-btn gt-btn-primary" id="gStartBtn" onclick="spamStart()"><i class="bi bi-play-fill"></i> Bắt Đầu</button>
              <button class="gt-btn gt-btn-danger" id="gStopBtn" onclick="spamStop()" disabled><i class="bi bi-stop-fill"></i> Dừng</button>
            </div>
            <div class="guard-log" id="gLog"><span class="log-info">// Sẵn sàng...</span></div>
          </div>
          <div class="gt-acc-body" id="spam-limit-body" style="display:none">
            <div class="text-center p-3">
              <p style="color:var(--t2); margin-bottom: 20px;">Bạn đã hết lượt dùng thử Spam Log vui lòng nâng gói Pro hoặc vượt link để nhận thêm 2 lần sử dụng miễn phí</p>
              <div class="d-flex gap-2 justify-content-center">
                <button class="gt-btn gt-btn-primary" onclick="window.scrollTo({top:0, behavior:'smooth'})">Mua Pro</button>
                <button class="gt-btn gt-btn-success" onclick="getBypassLink('spamlog')">Vượt link</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div> </div><div id="toastWrap"></div>

<footer style="text-align: center; padding: 24px; color: var(--t3); font-family: var(--mono); font-size: 12px; margin-top: 20px;">
  ©2026 VuaMXT | Powered by <span style="color: var(--acc); font-weight: 600;">PLongDeveloper</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API = 'proxy.php';
let _sessionUser = null;
let _statusInterval = null;

// ══ EMAIL FLOWS STATE ══
let _emailState = {
  ae: { verifier_token: null },
  re: { identity_token: null, method: null },
  ce: { identity_token: null, verifier_token: null, method: null }
};

function resetEmailState(key) {
  if (key === 'ae') {
    _emailState.ae = { verifier_token: null };
    document.getElementById('ae-step1').style.display = 'block';
    document.getElementById('ae-step2').style.display = 'none';
    document.getElementById('ae-step3').style.display = 'none';
  } else if (key === 're') {
    _emailState.re = { identity_token: null, method: null };
    document.getElementById('re-step1').style.display = 'block';
    document.getElementById('re-step2').style.display = 'none';
    document.getElementById('re-verify-input').style.display = 'none';
  } else if (key === 'ce') {
    _emailState.ce = { identity_token: null, verifier_token: null, method: null };
    document.getElementById('ce-step1').style.display = 'block';
    document.getElementById('ce-step2').style.display = 'none';
    document.getElementById('ce-step3').style.display = 'none';
    document.getElementById('ce-verify-input').style.display = 'none';
    document.getElementById('btn-final-ce').style.display = 'none';
  }
  const resBox = document.getElementById(`${key}-res-box`);
  if (resBox) resBox.classList.remove('show');
}

function setVerifyMethod(key, method) {
  _emailState[key].method = method;
  document.getElementById(`${key}-verify-input`).style.display = 'block';
  document.getElementById(`${key}-otp-box`).style.display = (method === 'otp' ? 'block' : 'none');
  document.getElementById(`${key}-sp-box`).style.display = (method === 'sp' ? 'block' : 'none');
}

window.addEventListener('DOMContentLoaded', () => { syncAuthStatus(); });

async function api(payload) {
  try {
    const r = await fetch(API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
    if (!r.ok) return { ok: false, msg: 'Lỗi HTTP ' + r.status };
    return await r.json();
  } catch (e) { return { ok: false, msg: 'Lỗi kết nối: ' + e.message }; }
}

function toast(msg, type='info') {
  const d = document.createElement('div');
  let icon = type === 'ok' ? '<i class="bi bi-check-circle-fill"></i>' : (type === 'err' ? '<i class="bi bi-exclamation-triangle-fill"></i>' : '<i class="bi bi-info-circle-fill"></i>');
  d.className = `gt-toast ${type}`; d.innerHTML = `${icon} <span>${msg}</span>`;
  const wrap = document.getElementById('toastWrap');
  if(wrap) { wrap.appendChild(d); setTimeout(() => d.remove(), 4000); }
}

function spin(id, on) { const e = document.getElementById(id); if(e) e.style.display = on?'inline-block':'none'; }

function showRes(id, data, ok) {
  const el = document.getElementById(id); if(!el) return;
  el.classList.add('show'); el.classList.toggle('ok', ok); el.classList.toggle('err', !ok);
  el.querySelector('span').textContent = typeof data === 'object' ? JSON.stringify(data, null, 2) : data;
}

function cpRes(id) {
  const t = document.getElementById(id)?.querySelector('span')?.textContent || '';
  navigator.clipboard.writeText(t).then(() => toast('Đã copy!', 'ok'));
}

function formatTime(seconds) {
  if (!seconds) return '0s';
  const d = Math.floor(seconds / 86400);
  const h = Math.floor((seconds % 86400) / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  const s = seconds % 60;
  let res = '';
  if (d > 0) res += d + 'd ';
  if (h > 0) res += h + 'h ';
  if (m > 0) res += m + 'm ';
  res += s + 's';
  return res;
}

async function syncAuthStatus() {
  const res = await api({ action: 'local_check_auth' });
  if (res.ok && res.user) { 
    _sessionUser = res.user; 
    renderAuthenticatedUI();
    if(_statusInterval) clearInterval(_statusInterval);
    _statusInterval = setInterval(refreshSpamStatus, 2000);
    refreshSpamStatus(true);
  } else { 
    _sessionUser = null; 
    renderGuestUI(); 
    if(_statusInterval) { clearInterval(_statusInterval); _statusInterval = null; }
  }
}

function renderAuthenticatedUI() {
  document.getElementById('authFormSection').style.display = 'none';
  document.getElementById('userProfileSection').style.display = 'block';
  document.getElementById('mainAppContent').classList.remove('disabled');
  document.getElementById('profileUser').textContent = _sessionUser.username;
  const isPro = parseInt(_sessionUser.is_pro) === 1;
  const badge = document.getElementById('profileBadge');
  if(badge) {
    badge.textContent = isPro ? 'PRO VIP' : 'FREE';
    badge.className = 'gt-tag ms-2 ' + (isPro ? 'pro-vip-badge' : 'warn-tag');
  }
  const quota = document.getElementById('spamQuotaText');
  if(quota) quota.textContent = isPro ? 'Đã dùng 0 / Vô hạn (PRO)' : `Đã dùng ${_sessionUser.spam_count} / 2 (FREE)`;
  
  const b7Quota = document.getElementById('ban7QuotaText');
  if(b7Quota) b7Quota.textContent = isPro ? 'Đã dùng 0 / Vô hạn (PRO)' : `Đã dùng ${_sessionUser.ban7_count} / 1 (FREE)`;

  const keyBlock = document.getElementById('keyActivationBlock');
  if(keyBlock) keyBlock.style.display = isPro ? 'none' : 'block';

  // Cập nhật hiển thị form hoặc thông báo hết lượt
  const isSpamLimited = !isPro && parseInt(_sessionUser.spam_count) >= 2;
  const isBan7Limited = !isPro && parseInt(_sessionUser.ban7_count) >= 1;

  document.getElementById('spam-main-body').style.display = isSpamLimited ? 'none' : 'block';
  document.getElementById('spam-limit-body').style.display = isSpamLimited ? 'block' : 'none';

  document.getElementById('b7-main-body').style.display = isBan7Limited ? 'none' : 'block';
  document.getElementById('b7-limit-body').style.display = isBan7Limited ? 'block' : 'none';
}

async function getBypassLink(feature) {
  toast('Đang khởi tạo link rút gọn...', 'info');
  const res = await api({ action: 'get_bypass_link', feature: feature });
  if (res.ok && res.shortenedUrl) {
    toast('Đang chuyển hướng tới link rút gọn...', 'ok');
    setTimeout(() => { window.location.href = res.shortenedUrl; }, 1000);
  } else {
    toast(res.msg || 'Lỗi không xác định', 'err');
  }
}

function renderGuestUI() {
  document.getElementById('authFormSection').style.display = 'block';
  document.getElementById('userProfileSection').style.display = 'none';
  document.getElementById('mainAppContent').classList.add('disabled');
}

function toggleAuthMode(isRegister) {
  document.getElementById('emailInputRow').style.display = isRegister ? 'block' : 'none';
  document.getElementById('authActionButtons').style.display = isRegister ? 'none' : 'flex';
  document.getElementById('authRegisterButtons').style.display = isRegister ? 'flex' : 'none';
  document.getElementById('authTitle').textContent = isRegister ? 'Đăng Ký Thành Viên Mới' : 'Hệ Thống Thành Viên';
}

async function submitAuth(type) {
  const u = document.getElementById('authUsername').value.trim();
  const p = document.getElementById('authPassword').value.trim();
  const e = document.getElementById('authEmail').value.trim();
  if(!u || !p) { toast('Vui lòng điền tài khoản và mật khẩu!', 'err'); return; }
  if(type === 'register' && !e) { toast('Vui lòng nhập thêm email!', 'err'); return; }
  
  toast('Đang xử lý...', 'info');
  const action = type === 'login' ? 'local_login' : 'local_register';
  const r = await api({ action, username: u, password: p, email: e });
  
  if(r.ok) {
    toast(r.msg, 'ok');
    if(type === 'login') { _sessionUser = r.user; renderAuthenticatedUI(); syncAuthStatus(); }
    else { toggleAuthMode(false); }
  } else { toast(r.msg, 'err'); }
}

async function submitLogout() { 
  if(_statusInterval) clearInterval(_statusInterval);
  await api({ action: 'local_logout' }); 
  _sessionUser = null; 
  renderGuestUI(); 
  toast('Đã thoát tài khoản', 'info'); 
}

async function submitActivateKey() {
  const k = document.getElementById('proLicenseKey').value.trim();
  if(!k) return toast('Nhập key!', 'err');
  const r = await api({ action: 'local_activate_key', key: k });
  if(r.ok) { toast(r.msg, 'ok'); syncAuthStatus(); } else { toast(r.msg, 'err'); }
}

function getAT(id) { return document.getElementById(id)?.value.trim() || ''; }

function gLog(msg,type='ok'){
  const el=document.getElementById('gLog');
  const s=document.createElement('span');
  s.className=`log-${type}`;
  s.textContent=`[${new Date().toLocaleTimeString('vi')}] ${msg}\n`;
  el.appendChild(s); el.scrollTop=el.scrollHeight;
}

// ══ TOOLS ══
async function doCheckEmail() {
  const at = getAT('em-at');
  if(!at) return toast('Nhập token!', 'err');

  spin('em-sp', true);

  const r = await api({
    action:'check_email',
    access_token:at
  });

  spin('em-sp', false);

  if(r.ok) {

    let out = '';

    const email = r.data.email ? r.data.email : 'Chưa có!';
    const pending = r.data.pending ? r.data.pending : 'Chưa có!';

    // UI mã bảo mật bị che
    const securityCode = `
🔐 Mã bảo mật:
<div style="display:flex;gap:6px;margin:8px 0;">
  <div style="width:34px;height:42px;border-radius:8px;background:#1e1e1e;border:1px solid #333;display:flex;align-items:center;justify-content:center;font-size:20px;">*</div>
  <div style="width:34px;height:42px;border-radius:8px;background:#1e1e1e;border:1px solid #333;display:flex;align-items:center;justify-content:center;font-size:20px;">*</div>
  <div style="width:34px;height:42px;border-radius:8px;background:#1e1e1e;border:1px solid #333;display:flex;align-items:center;justify-content:center;font-size:20px;">*</div>
  <div style="width:34px;height:42px;border-radius:8px;background:#1e1e1e;border:1px solid #333;display:flex;align-items:center;justify-content:center;font-size:20px;">*</div>
  <div style="width:34px;height:42px;border-radius:8px;background:#1e1e1e;border:1px solid #333;display:flex;align-items:center;justify-content:center;font-size:20px;">*</div>
  <div style="width:34px;height:42px;border-radius:8px;background:#1e1e1e;border:1px solid #333;display:flex;align-items:center;justify-content:center;font-size:20px;">*</div>
</div>

<div style="color:#ffcc00;font-size:13px;">
⚡ Vui lòng mở khoá Pro để xem mã bảo mật
</div>
`;

    if (r.data.email) {

      out = `
✅ Email khôi phục: ${email}
🔒 Trạng thái: Đã được gắn ✅

${securityCode}
`;

    } else if (r.data.pending) {

      out = `
Email khôi phục: ${email}
📨 Email chờ: ${pending}
⏰ Thời gian chờ: ${formatTime(r.data.countdown)}

${securityCode}
`;

    } else {

      out = `
Email khôi phục: ${email}
Email chờ: ${pending}

${securityCode}
`;

    }

    showRes('em-res', out, true);

  } else {

    toast(r.msg, 'err');

  }
}


async function doCheckPlatforms() {
  const at = getAT('pl-at'); if(!at) return toast('Nhập token!', 'err');
  spin('pl-sp', true); const r = await api({action:'check_platforms', access_token:at}); spin('pl-sp', false);
  if(r.ok) {
    let h = '<div class="gt-res show ok">';
    h += '<div style="color:var(--blu); font-weight:700; font-size:11px; margin-bottom:8px">🔗 Liên kết phụ:</div>';
    
    if (r.data.linked && r.data.linked.length > 0) {
      r.data.linked.forEach(l => {
        h += `<div style="margin-bottom:6px">▶ <b style="color:var(--grn)">${l.platform}</b>`;
        if(l.email) h += `<br><span style="color:var(--t2); padding-left:12px">📧 ${l.email}</span>`;
        if(l.nick) h += `<br><span style="color:var(--t2); padding-left:12px">📝 ${l.nick}</span>`;
        h += '</div>';
      });
    } else {
      h += '<div style="color:var(--acc); font-size:11px">⚠️ Không có liên kết phụ nào trong tài khoản này</div>';
    }
    
    h += '<hr style="border-color:var(--bd); margin:12px 0">';
    h += '<div style="color:var(--blu); font-weight:700; font-size:11px; margin-bottom:8px">🎮 Liên kết chính:</div>';
    h += `<div style="color:var(--grn); font-weight:700">▶ ${r.data.main || 'Unknown'}</div>`;
    h += '</div>';
    
    document.getElementById('pl-tbl').innerHTML = h;
  } else toast(r.msg, 'err');
}

async function doBan7() {
  const at = getAT('b7-at'); 
  if(!at) return toast('Nhập token!', 'err');
  if(!confirm('Cảnh báo: Bạn sắp kích hoạt cơ chế Ban 7 Ngày. Tiếp tục?')) return;
  spin('b7-sp', true); 
  const r = await api({action:'ban7', access_token:at}); 
  spin('b7-sp', false);
  if(r.ok) {
    showRes('b7-res', r.msg + `\nNickname: ${r.data.nickname}\nUID: ${r.data.account_id}`, true);
    toast('Đã thực thi thành công!', 'ok');
    syncAuthStatus(); 
  } else { toast(r.msg, 'err'); }
}

// ══ SPAM ══
async function spamStart() {
  const at = getAT('sp-at'), iv = parseInt(document.getElementById('sp-iv').value);
  const days = parseInt(document.getElementById('tDays').value) || 0;
  const hours = parseInt(document.getElementById('tHours').value) || 0;
  const mins = parseInt(document.getElementById('tMins').value) || 0;
  const totalMs = (days * 86400 + hours * 3600 + mins * 60) * 1000;
  const maxMs = 15 * 86400 * 1000;

  if(!at) return toast('Nhập token!', 'err');
  if(totalMs <= 0) return toast('Nhập thời gian chạy!', 'err');
  if(totalMs > maxMs) return toast('Thời gian chạy tối đa là 15 ngày!', 'err');
  
  document.getElementById('gStartBtn').disabled = true;
  toast('Đang gửi lệnh khởi tạo...', 'info');
  
  const r = await api({ action:'spam_init', access_token:at, interval:iv, duration_ms: totalMs });
  if(r.ok) {
    // LƯU TOKEN VÀO TRÌNH DUYỆT ĐỂ KHÔNG BỊ QUÊN KHI F5
    localStorage.setItem('running_spam_at', at);
    
    toast('Đã kích hoạt chạy ngầm!', 'ok');
    updateSpamUI(r.data);
    if(_statusInterval) clearInterval(_statusInterval);
    _statusInterval = setInterval(refreshSpamStatus, 2000);
  } else {
    toast(r.msg, 'err');
    document.getElementById('gStartBtn').disabled = false;
  }
}

function updateSpamUI(data) {
  const sBtn = document.getElementById('gStartBtn');
  const stBtn = document.getElementById('gStopBtn');
  const ring = document.getElementById('gRing');
  const status = document.getElementById('gStatus');
  const ip = document.getElementById('gIp');

  if(!data || data.status === 'idle' || data.status === 'stopped' || data.status === 'finished') {
    ring.classList.remove('live');
    status.textContent = 'IDLE';
    ip.textContent = '--';
    sBtn.disabled = false;
    stBtn.disabled = true;
    return;
  }
  
  ring.classList.add('live');
  status.textContent = 'SpamLogging';
  ip.textContent = `${data.ip}:${data.port}`;
  document.getElementById('gSent').textContent = data.sent || 0;
  document.getElementById('gOk').textContent = data.ok || 0;
  document.getElementById('gFail').textContent = data.fail || 0;
  
  sBtn.disabled = true;
  stBtn.disabled = false;
  
  if(data.at && !document.getElementById('sp-at').value) { document.getElementById('sp-at').value = data.at; }
}

let _idleCount = 0;
async function refreshSpamStatus(firstLoad = false) {
  if(!_sessionUser) return;
  const r = await api({action:'spam_status'});
  if(r.ok) {
    if(r.data.status === 'idle' || r.data.status === 'stopped' || r.data.status === 'finished') {
      _idleCount++;
      if(_idleCount >= 2 || firstLoad) { 
        updateSpamUI(r.data); 
        localStorage.removeItem('running_spam_at'); // Xóa token nếu đã dừng
      }
    } else {
      _idleCount = 0;
      updateSpamUI(r.data);
      if(firstLoad) {
        toast('Đã khôi phục tiến trình Spam!', 'ok');
        gLog('Hệ thống: Đã khôi phục trạng thái spam đang chạy.', 'info');
      }
    }
  }
}

async function spamStop() { 
  const stBtn = document.getElementById('gStopBtn');
  stBtn.disabled = true;
  toast('Đang gửi lệnh dừng...', 'info');
  const r = await api({action:'spam_stop'}); 
  if(r.ok) {
    // XÓA TOKEN KHỎI BỘ NHỚ TRÌNH DUYỆT
    localStorage.removeItem('running_spam_at');
    
    toast('Đã dừng tiến trình!', 'ok');
    updateSpamUI({status: 'stopped'});
  } else {
    toast(r.msg, 'err');
    stBtn.disabled = false;
  }
}

// ══ CONVERT ══
async function doEatToAccess() {
  const v = document.getElementById('eat-val').value.trim(); if(!v) return toast('Nhập EAT!', 'err');
  spin('eat-sp', true); const r = await api({action:'eat_to_access', eat:v}); spin('eat-sp', false);
  if(r.ok) { showRes('eat-res', r.data, true); toast('Thành công', 'ok'); } else toast(r.msg, 'err');
}

async function doEatToJwt() {
  const v = document.getElementById('eat-val').value.trim(); if(!v) return toast('Nhập EAT!', 'err');
  spin('eat-sp', true); const r = await api({action:'eat_to_jwt', eat:v}); spin('eat-sp', false);
  if(r.ok) { showRes('eat-res', r.data.jwt, true); toast('Thành công', 'ok'); } else toast(r.msg, 'err');
}

async function doAccessToJwt() {
  const at = getAT('atj-at'); if(!at) return toast('Nhập token!', 'err');
  spin('atj-sp', true); const r = await api({action:'access_to_jwt', access_token:at}); spin('atj-sp', false);
  if(r.ok) { showRes('atj-res', r.data.jwt, true); toast('Thành công', 'ok'); } else toast(r.msg, 'err');
}

async function doGuestJwt() {
  const uid_g = document.getElementById('gu-uid').value.trim(), pw = document.getElementById('gu-pw').value.trim();
  if(!uid_g || !pw) return toast('Điền UID and password!', 'err');
  spin('gu-sp', true); const r = await api({action:'guest_to_jwt', uid_guest:uid_g, password:pw}); spin('gu-sp', false);
  if(r.ok) { showRes('gu-res', r.data.jwt, true); toast('Thành công', 'ok'); } else toast(r.msg, 'err');
}

async function doRevoke() {
  const at = getAT('rv-at'); if(!at) return toast('Nhập token!', 'err');
  if(!confirm('Xác nhận vô hiệu hoá này?')) return;
  spin('rv-sp', true); const r = await api({action:'revoke_token', access_token:at}); spin('rv-sp', false);
  if(r.ok) { showRes('rv-res', r.msg, true); toast('Đã vô hiệu hoá token này!', 'ok'); } else toast(r.msg, 'err');
}

async function doLongBio() {
  const method = document.getElementById('lb-method').value;
  const bio = document.getElementById('lb-bio').value.trim();
  if(!bio) return toast('Nhập nội dung bio!', 'err');
  
  const payload = { action: 'long_bio', bio: bio };
  
  if (method === 'at') {
      const at = document.getElementById('lb-tok').value.trim();
      if(!at) return toast('Nhập Access Token!', 'err');
      payload.access_token = at;
  } else if (method === 'jwt') {
      const jwt = document.getElementById('lb-tok').value.trim();
      if(!jwt) return toast('Nhập JWT Token!', 'err');
      payload.jwt = jwt;
  } else if (method === 'guest') {
      const uid = document.getElementById('lb-uid').value.trim();
      const pass = document.getElementById('lb-pass').value.trim();
      if(!uid || !pass) return toast('Nhập UID và Password!', 'err');
      payload.uid_guest = uid;
      payload.password = pass;
  }
  
  spin('lb-sp', true);
  const r = await api(payload);
  spin('lb-sp', false);
  if(r.ok) { showRes('lb-res', r.msg, true); toast('Cập nhật Bio thành công!', 'ok'); } else toast(r.msg, 'err');
}

function toggleLBFields() {
  const m = document.getElementById('lb-method').value;
  const fieldAtJwt = document.getElementById('lb-field-atjwt');
  const fieldGuest = document.getElementById('lb-field-guest');
  const lblTok = document.getElementById('lb-tok-label');
  
  if (m === 'guest') {
    fieldAtJwt.style.display = 'none';
    fieldGuest.style.display = 'block';
  } else {
    fieldAtJwt.style.display = 'block';
    fieldGuest.style.display = 'none';
    lblTok.textContent = (m === 'at' ? 'Access Token' : 'JWT Token');
  }
}

// ══ EMAIL ACTION HELPERS ══
async function doSendOtp() {
  const at = getAT('ae-at'), em = document.getElementById('ae-email').value.trim();
  if(!at || !em) return toast('Thiếu thông tin!', 'err');
  spin('ae-sp', true);
  const r = await api({ action: 'send_otp', access_token: at, email: em });
  spin('ae-sp', false);
  if(r.ok) {
    toast(r.msg, 'ok');
    document.getElementById('ae-step1').style.display = 'none';
    document.getElementById('ae-step2').style.display = 'block';
  } else toast(r.msg, 'err');
}

async function doVerifyOtp() {
  const at = getAT('ae-at'), em = document.getElementById('ae-email').value.trim();
  const otp = document.getElementById('ae-otp').value.trim();
  if(!otp) return toast('Nhập OTP!', 'err');
  spin('vo-sp', true);
  const r = await api({ action: 'verify_otp', access_token: at, email: em, otp: otp });
  spin('vo-sp', false);
  if(r.ok && r.data.verifier_token) {
    _emailState.ae.verifier_token = r.data.verifier_token;
    toast('OTP OK!', 'ok');
    document.getElementById('ae-step2').style.display = 'none';
    document.getElementById('ae-step3').style.display = 'block';
  } else toast(r.msg, 'err');
}

async function doCreateBind() {
  const at = getAT('ae-at'), em = document.getElementById('ae-email').value.trim();
  const sp = document.getElementById('ae-sp2').value.trim();
  const vt = _emailState.ae.verifier_token;
  if(!sp) return toast('Nhập mã bảo mật!', 'err');
  spin('cb-sp', true);
  const r = await api({ action: 'create_bind', access_token: at, email: em, verifier_token: vt, sec_pw: sp });
  spin('cb-sp', false);
  if(r.ok) { showRes('ae-res-box', r.msg, true); toast('Thành công!', 'ok'); } else toast(r.msg, 'err');
}

async function doSendOtpForVerify(key) {
  const at = getAT(`${key}-at`), em = document.getElementById(`${key}-email`).value.trim();
  if(!at || !em) return toast('Thiếu thông tin!', 'err');
  toast('Đang gửi OTP...', 'info');
  const r = await api({ action: 'send_otp', access_token: at, email: em });
  if(r.ok) toast(r.msg, 'ok'); else toast(r.msg, 'err');
}

async function doVerifyIdentity(key, type) {
  const at = getAT(`${key}-at`), em = document.getElementById(`${key}-email`).value.trim();
  const method = _emailState[key].method;
  const payload = { action: 'verify_identity', access_token: at, email: em };
  
  if (method === 'otp') {
    payload.otp = document.getElementById(`${key}-otp`).value.trim();
    if(!payload.otp) return toast('Nhập OTP!', 'err');
  } else {
    payload.sec_pw = document.getElementById(`${key}-sp${key==='ce'?'2':''}`).value.trim();
    if(!payload.sec_pw) return toast('Nhập mã bảo mật!', 'err');
  }

  const spinId = key === 're' ? 'ri-sp' : 'ci-sp';
  spin(spinId, true);
  const r = await api(payload);
  spin(spinId, false);

  if(r.ok && r.data.identity_token) {
    _emailState[key].identity_token = r.data.identity_token;
    toast('Xác minh OK!', 'ok');
    document.getElementById(`${key}-step1`).style.display = 'none';
    document.getElementById(`${key}-step2`).style.display = 'block';
  } else toast(r.msg, 'err');
}

async function doCreateUnbind() {
  const at = getAT('re-at'), it = _emailState.re.identity_token;
  spin('cu-sp', true);
  const r = await api({ action: 'create_unbind', access_token: at, identity_token: it });
  spin('cu-sp', false);
  if(r.ok) { showRes('re-res-box', r.msg, true); toast('Thành công!', 'ok'); } else toast(r.msg, 'err');
}

async function doSendOtpChange() {
  const at = getAT('ce-at'), nem = document.getElementById('ce-newemail').value.trim();
  if(!nem) return toast('Nhập email mới!', 'err');
  spin('cos-sp', true);
  const r = await api({ action: 'send_otp', access_token: at, email: nem });
  spin('cos-sp', false);
  if(r.ok) {
    toast(r.msg, 'ok');
    document.getElementById('ce-step2').style.display = 'none';
    document.getElementById('ce-step3').style.display = 'block';
  } else toast(r.msg, 'err');
}

async function doVerifyOtpChange() {
  const at = getAT('ce-at'), nem = document.getElementById('ce-newemail').value.trim();
  const otp = document.getElementById('ce-newotp').value.trim();
  spin('cov-sp', true);
  const r = await api({ action: 'verify_otp', access_token: at, email: nem, otp: otp });
  spin('cov-sp', false);
  if(r.ok && r.data.verifier_token) {
    _emailState.ce.verifier_token = r.data.verifier_token;
    toast('OTP OK!', 'ok');
    document.getElementById('btn-final-ce').style.display = 'inline-flex';
  } else toast(r.msg, 'err');
}

async function doCreateRebind() {
  const at = getAT('ce-at'), nem = document.getElementById('ce-newemail').value.trim();
  const it = _emailState.ce.identity_token, vt = _emailState.ce.verifier_token;
  spin('cr-sp', true);
  const r = await api({ action: 'create_rebind', access_token: at, identity_token: it, verifier_token: vt, new_email: nem });
  spin('cr-sp', false);
  if(r.ok) { showRes('ce-res-box', r.msg, true); toast('Thành công!', 'ok'); } else toast(r.msg, 'err');
}

async function doCancelEmail() {
  const at = getAT('ca-at'); if(!at) return toast('Nhập token!', 'err');
  spin('ca-sp', true); const r = await api({action:'cancel_email', access_token:at}); spin('ca-sp', false);
  if(r.ok) { showRes('ca-res', r.msg, true); toast('Đã hủy!', 'ok'); } else toast(r.msg, 'err');
}

// TỰ ĐỘNG KHÔI PHỤC INPUT TOKEN KHI TẢI LẠI TRANG
window.addEventListener('DOMContentLoaded', () => {
    const savedToken = localStorage.getItem('running_spam_at');
    const inputToken = document.getElementById('sp-at');
    if (savedToken && inputToken && !inputToken.value) {
        inputToken.value = savedToken;
    }
});
</script>
</body>
</html>
