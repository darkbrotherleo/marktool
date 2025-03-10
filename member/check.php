<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require '../src/auth.php'; // Kiểm tra quyền truy cập
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check CODE Sản Phẩm - LegitHPS</title>
    <link rel="stylesheet" href="../assets/css/check.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

</head>
<body>
    <div class="container">
        <h2>Check CODE Sản Phẩm</h2>
        <form id="checkForm" method="POST">
            <div class="form-group">
                <label for="code">Nhập CODE:</label>
                <input type="text" id="code" name="code" required placeholder="Nhập mã CODE">
            </div>
            <button type="submit" class="check-btn">Kiểm Tra</button>
        </form>

        <div id="resultBox" class="result-box" style="display: none;"></div>
    </div>
    <script src="../assets/js/check.js"></script>
</body>
</html>
