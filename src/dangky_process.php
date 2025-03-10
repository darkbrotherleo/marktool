<?php
session_start();
// Kết nối đến cơ sở dữ liệu
require '../includes/db_connect.php';

try {
    // Lấy kết nối
    $conn = get_db_connection();

    // Kiểm tra xem có dữ liệu gửi đến không
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Lấy dữ liệu từ biểu mẫu
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Kiểm tra tính hợp lệ của dữ liệu
        if (empty($fullname) || empty($email) || empty($password)) {
            throw new Exception("Vui lòng điền đầy đủ thông tin.");
        }

        // Kiểm tra xem email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            throw new Exception("Email đã tồn tại. Vui lòng sử dụng email khác.");
        }

        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Chuẩn bị câu lệnh SQL để chèn dữ liệu
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $hashed_password);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            echo "Đăng ký thành công!";
            header("Location: ../member/dangnhap.php");
        } else {
            throw new Exception("Lỗi: " . $stmt->error);
        }

        // Đóng câu lệnh
        $stmt->close();
    }
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
} finally {
    // Đóng kết nối
    close_db_connection($conn);
}
?>