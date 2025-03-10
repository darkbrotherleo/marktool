<?php session_start(); // Đảm bảo dòng này có ở đầu tệp ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Đăng Nhập</title>
    <meta name="csrf-token" content="<?php htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <link rel="stylesheet" href="../assets/css/dangnhap.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <div class="container">
        <form class="login-form" action="../src/dangnhap_process.php" method="POST">
            <h2>Đăng Nhập</h2>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="Nhập Email" required>
            </div>
            <div class="input-group">
                <label for="password">Mật Khẩu</label>
                <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
            </div>
            <button type="submit">Đăng Nhập</button>
            <p class="message">Bạn chưa có tài khoản? <a href="../member/dangky.php">Đăng ký</a></p>
        </form>
    </div>

    <!-- Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="errorMessage"></p>
            <button class="btn" onclick="redirectToLogin()">Quay lại</button>
        </div>
    </div>

    <script src="../assets/js/dangnhap.js"></script>
</body>
</html>