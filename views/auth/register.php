<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Workspace</title>
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
        .auth-links a { color: #2383e2; text-decoration: none; font-weight: 500; }
        .auth-links a:hover { text-decoration: underline; }
        .alert { padding: 10px; border-radius: 6px; font-size: 0.9rem; text-align: center; margin-bottom: 15px; }
        .alert-error { background: #fde8e8; color: #eb3639; border: 1px solid #f9c2c2; }
        .alert-success { background: #dbeddb; color: #0f7b6c; border: 1px solid #c3e2c3; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1 class="auth-title">Đăng ký tài khoản</h1>
        <p class="auth-subtitle">Tạo Workspace của riêng bạn</p>

        <?php if(!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="index.php?action=register" method="POST">
            <div class="form-group">
                <label>Họ và Tên</label>
                <input type="text" name="fullname" class="form-control" placeholder="Tên của sếp..." required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="vidu@email.com" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
            </div>
            <button type="submit" class="btn-submit">Tạo tài khoản</button>
        </form>

        <div class="auth-links">
            Đã có tài khoản? <a href="index.php?action=login">Đăng nhập</a>
        </div>
    </div>
</body>
</html>