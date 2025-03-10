
<?php
    session_start();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    require './public/header.php';
?>
<body>
<div class="container">
<div class="banner-container">
    <img src="./uploads/67c0b56a78148_Banner-Bao-Hanh-1536x668.png" alt="Emmié by HappySkin" class="banner-img">
    <div class="qr-code">
        <p>Quét mã QR để được hỗ trợ bảo hành nhanh chóng nhất!!!</p>
        <p><a href="./admin/dashboard.php">Trang Admin</a></p>
        <p><a href="./src/dangxuat.php">Đăng Xuất</a></p>
</div>
</div>
    <div class="columns">
        <?php include './public/content_index.php'; ?>
        <div class="column">
            <div class="check-form">
                <?php
                    // legithps/index.php
                    if (isset($_GET['action']) && $_GET['action'] === 'check_process') {
                        require_once './src/activate_process.php';
                        exit;
                    }
                    // Nếu không có action, chuyển hướng hoặc hiển thị trang mặc định
                    require_once './public/activate.php';
                ?>
            </div>
        </div>
    </div>
    <?php include './public/footer.php'; ?>
    </div>
</body>
</html>
</body>
</html>