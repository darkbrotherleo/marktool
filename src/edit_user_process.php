<?php
require_once '../includes/db_connect.php';
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa và có vai trò admin không
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../member/dangnhap.php'); // Chuyển hướng đến trang đăng nhập
    exit;
}

try {
    $conn = get_db_connection();
    
    // Lấy dữ liệu từ form
    $id = $_POST['id'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Kiểm tra và làm sạch dữ liệu đầu vào
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $role = filter_var($role, FILTER_SANITIZE_STRING);
    $status = filter_var($status, FILTER_SANITIZE_NUMBER_INT);

    // Cập nhật thông tin người dùng
    $sql = "UPDATE users SET email = ?, role = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ssii", $email, $role, $status, $id);
        
        if ($stmt->execute()) {
            echo '<div class="alert alert-success">Cập nhật thông tin thành công.</div>';
        } else {
            echo '<div class="alert alert-danger">Cập nhật thông tin thất bại: ' . $stmt->error . '</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Lỗi trong câu lệnh SQL: ' . $conn->error . '</div>';
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
} finally {
    // Đóng kết nối
    if (isset($conn)) {
        close_db_connection($conn);
    }
}

// Chuyển hướng về trang quản lý tài khoản sau khi cập nhật
header('Location: ../admin/dashboard.php');
exit;
?>