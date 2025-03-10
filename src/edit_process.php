<?php
// admin/edit_process.php
require_once '../includes/db_connect.php';
session_start();
// Kiểm tra nếu form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $code = $_POST['code'] ?? '';
    $serialNumber = $_POST['SerialNumber'] ?? '';
    $customerName = $_POST['CustomerName'] ?? '';
    $phoneNumber = $_POST['PhoneNumber'] ?? '';
    $email = $_POST['Email'] ?? '';
    $purchaseLocation = $_POST['PurchaseLocation'] ?? '';
    $cityProvince = $_POST['CityProvince'] ?? '';
    $isChecked = $_POST['IsChecked'] ?? 0;
    
    // Validate dữ liệu
    if (empty($code)) {
        die("Mã code không được để trống!");
    }
    
    try {
        $conn = get_db_connection();
        
        // Chuẩn bị câu lệnh SQL
        $sql = "UPDATE customerdatabase SET 
                SerialNumber = ?, 
                CustomerName = ?, 
                PhoneNumber = ?, 
                Email = ?, 
                PurchaseLocation = ?, 
                CityProvince = ?, 
                IsChecked = ? 
                WHERE Code = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $serialNumber, $customerName, $phoneNumber, $email, $purchaseLocation, $cityProvince, $isChecked, $code);
        
        // Thực thi câu lệnh
        if ($stmt->execute()) {
            // Chuyển hướng về trang dashboard với thông báo thành công
            header("Location: ../admin/dashboard.php?message=update_success");
            exit;
        } else {
            throw new Exception("Lỗi khi cập nhật dữ liệu: " . $stmt->error);
        }
    } catch (Exception $e) {
        die("Lỗi: " . $e->getMessage());
    } finally {
        // Đóng kết nối
        if (isset($conn)) {
            close_db_connection($conn);
        }
    }
} else {
    // Nếu không phải POST request, chuyển hướng về trang dashboard
    header("Location: ../admin/dashboard.php");
    exit;
}
?>