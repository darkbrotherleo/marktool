<?php
// Kết nối database
require_once '../includes/db_connect.php'; // Cập nhật đường dẫn file kết nối database
session_start();
$conn = get_db_connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lệnh TRUNCATE TABLE
    $sql = "TRUNCATE TABLE customerdatabase"; 

    // Thực thi truy vấn
    if ($conn->query($sql) === TRUE) {
        echo "<h3>Dữ liệu trong bảng `Thông Tin Kích Hoạt` đã được xóa thành công!</h3>";
    } else {
        echo "<h3>Lỗi: Không thể xóa dữ liệu. Chi tiết lỗi:</h3> <p>" . $conn->error . "</p>";
    }

    // Đóng kết nối database
    $conn->close();
    header("Location: ../admin/dashboard.php");
}
?>
