<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu - Workspace</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f7f7f5; margin: 0; color: #37352f; }
        .auth-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 100%; max-width: 380px; border: 1px solid #e3e2e0; }
        .auth-title { margin-top: 0; margin-bottom: 5px; font-size: 1.5rem; text-align: center; }
        .auth-subtitle { color: #787774; font-size: 0.9rem; text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.85rem; color: #787774; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid #e3e2e0; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem; font-family: inherit; transition: all 0.2s ease; }
        .form-control:focus { border-color: #2383e2; outline: none; box-shadow: 0 0 0 3px rgba(35, 131, 226, 0.15); }
        .btn-submit { width: 100%; padding: 12px; background: #2383e2; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 0.95rem; margin-top: 10px; transition: background 0.2s; }
        .btn-submit:hover { background: #1a6ab5; }
        .auth-links { text-align: center; margin-top: 20px; font-size: 0.9rem; color: #787774; }
        .auth-links a { color: #2383e2; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
        .auth-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">Khôi phục mật khẩu</h1>
        <p class="auth-subtitle">Nhập Email tài khoản và mật khẩu mới muốn đổi.</p>
        
        <form action="index.php?action=forgot-password" method="POST">
            <div class="form-group">
                <label>Email đã đăng ký</label>
                <input type="email" name="email" class="form-control" placeholder="vidu@email.com" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu mới</label>
                <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới..." required>
            </div>
            <button type="submit" class="btn-submit">Cập nhật mật khẩu</button>
        </form>
        
        <div class="auth-links">
            <a href="index.php?action=login">Quay lại Đăng nhập</a>
        </div>
    </div>
</body>
</html>