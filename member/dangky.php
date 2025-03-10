<?php session_start(); // Đảm bảo dòng này có ở đầu tệp ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Đăng Ký Tài Khoản</title>
    <meta name="csrf-token" content="<?php htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <link rel="stylesheet" href="../assets/css/dangky.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <div class="container">
        <form class="register-form" action="../src/dangky_process.php" method="POST">
            <h2>Đăng Ký Tài Khoản</h2>
            <div class="input-group">
                <label for="fullname">Họ Tên</label>
                <input type="text" id="fullname" name="fullname" placeholder="Nhập họ tên" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Nhập email" required>
            </div>
            <div class="input-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit">Đăng Ký</button>
            <p class="message">Bạn đã có tài khoản? <a href="#">Đăng nhập</a></p>
        </form>
    </div>
</body>
</html>