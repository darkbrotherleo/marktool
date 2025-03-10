<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa và có vai trò admin không
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../member/dangnhap.php'); // Chuyển hướng đến trang đăng nhập
    exit;
}

require_once '../includes/db_connect.php';

// Lấy email từ URL và giải mã
$email = isset($_GET['email']) ? urldecode($_GET['email']) : '';

try {
    $conn = get_db_connection();
    
    // Truy vấn thông tin người dùng dựa trên email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo '<div class="alert alert-warning">Không tìm thấy tài khoản với email này.</div>';
        exit;
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
} finally {
    // Đóng kết nối
    if (isset($conn)) {
        close_db_connection($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Thông Tin Người Dùng</title>
    <link rel="stylesheet" href="../assets/css/edit_user.css">
</head>
<body>
    <div class="container">
        <h2>Chỉnh Sửa Thông Tin Người Dùng</h2>
        <form action="../src/edit_user_process.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="role">Vai Trò:</label>
                <select id="role" name="role">
                    <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User </option>
                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Trạng Thái:</label>
                <select id="status" name="status">
                    <option value="1" <?php echo ($user['status'] == 1) ? 'selected' : ''; ?>>Kích hoạt</option>
                    <option value="0" <?php echo ($user['status'] == 0) ? 'selected' : ''; ?>>Tắt kích hoạt</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-save">LƯU</button>
                <a href="../admin/account_management.php" class="btn-cancel">HỦY</a>
            </div>
        </form>
    </div>

    <!-- Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Chỉnh Sửa Thông Tin Người Dùng</h2>
            <form id="editForm" action="../src/edit_user_process.php" method="POST">
                <input type="hidden" name="id" id="userId">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="role">Vai Trò:</label>
                    <select id="role" name="role">
                        <option value="user">User </option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Trạng Thái:</label>
                    <select id="status" name="status">
                        <option value="1">Kích hoạt</option>
                        <option value="0">Tắt kích hoạt</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save">LƯU</button>
                    <button type="button" class="btn-cancel close">HỦY</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>