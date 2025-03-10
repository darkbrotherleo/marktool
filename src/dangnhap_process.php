<?php
// Kết nối đến cơ sở dữ liệu
require '../includes/db_connect.php';

session_start();

try {
    // Lấy kết nối
    $conn = get_db_connection();

    // Kiểm tra xem có dữ liệu gửi đến không
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Lấy dữ liệu từ biểu mẫu
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Kiểm tra tính hợp lệ của dữ liệu
        if (empty($email) || empty($password)) {
            $error_message = "Vui lòng điền đầy đủ thông tin.";
            header("Location: ../member/dangnhap.php?error=" . urlencode($error_message));
            exit();
        }

        // Chuẩn bị câu lệnh SQL để lấy thông tin người dùng
        $stmt = $conn->prepare("SELECT id, fullname, password, status, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Kiểm tra xem email có tồn tại không
        if ($stmt->num_rows === 0) {
            $error_message = "Email không tồn tại.";
            header("Location: ../member/dangnhap.php?error=" . urlencode($error_message));
            exit();
        }

        // Lấy thông tin người dùng
        $stmt->bind_result($user_id, $fullname, $hashed_password, $status, $role);
        $stmt->fetch();

        // Kiểm tra trạng thái tài khoản
        if ($status == 0) {
            $error_message = "Tài khoản chưa được kích hoạt. Vui lòng liên hệ với quản trị viên.";
            header("Location: ../member/dangnhap.php?error=" . urlencode($error_message));
            exit();
        }

        // Kiểm tra mật khẩu
        if (!password_verify($password, $hashed_password)) {
            $error_message = "Mật khẩu không chính xác.";
            header("Location: ../member/dangnhap.php?error=" . urlencode($error_message));
            exit();
        }

        // Đăng nhập thành công
        $_SESSION['id'] = $user_id; // Sử dụng đúng biến
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        $_SESSION['fullname'] = $fullname;

        // Chuyển hướng theo vai trò
        if ($role == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../member/check.php");
        }
        exit();
    }
} catch (Exception $e) {
    $error_message = "Lỗi: " . $e->getMessage();
    header("Location: ../member/dangnhap.php?error=" . urlencode($error_message));
} finally {
    // Đóng kết nối
    if (isset($conn)) {
        close_db_connection($conn);
    }
}
?>