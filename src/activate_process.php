<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

function sendResponse($success, $message, $extra = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit();
}

try {
    $conn = get_db_connection();
    if (!$conn) {
        throw new Exception('Không thể kết nối tới cơ sở dữ liệu.');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        sendResponse(false, '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Dữ liệu không hợp lệ.</p>');
    }

    if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
        sendResponse(false, '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Xác thực không hợp lệ (CSRF token).</p>');
    }

    $code = trim($data['code'] ?? '');
    if (empty($code)) {
        sendResponse(false, '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Vui lòng nhập mã CODE.</p>');
    }

    $customer_name = trim($data['customer_name'] ?? '');
    $phone_number = trim($data['phone_number'] ?? '');
    $email = trim($data['email'] ?? '');
    $purchase_location = trim($data['purchase_location'] ?? '');
    $city_province = trim($data['city_province'] ?? '');

    // Kiểm tra mã trong DB
    $stmt = $conn->prepare("SELECT IsChecked FROM customerdatabase WHERE Code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendResponse(false, '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Code không tồn tại trên hệ thống, vui lòng liên hệ bộ phận CSKH để được giải quyết.</p>');
    }

    $row = $result->fetch_assoc();
    if ((int)$row['IsChecked'] === 1) {
        sendResponse(false, '<strong>THÔNG BÁO:</strong><p style="color:green;font-weight:600;">Sản Phẩm Đã Kiểm Tra Xác Thực Chính Hãng Trước Đó.</p>', ['already_activated' => true]);
    }

    // Cập nhật mã chưa kích hoạt
    $stmt = $conn->prepare("UPDATE customerdatabase SET 
        CustomerName = ?, 
        PhoneNumber = ?, 
        Email = ?, 
        PurchaseLocation = ?, 
        CityProvince = ?, 
        IsChecked = 1, 
        CheckIP = ?, 
        CheckTime = NOW() 
        WHERE Code = ? AND IsChecked = 0");
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("sssssss", $customer_name, $phone_number, $email, $purchase_location, $city_province, $ip, $code);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Sản Phẩm Chính Hãng Của Emmié by HappySkin.</p><p style="color:red;font-weight:600;">Emmié gửi tặng Quý Khách Hàng mã giảm thêm 5% tối đa 15k khi mua sản phẩm Emmié tại website: <a href="happyskin.vn">happyskin.vn</a></p><em style="font-weight:600;">* Mã voucher giảm giá là mã code tại lớp tráng bạc của tem.</em>';
        sendResponse(true, $successMessage, ['newly_activated' => true, 'voucher_code' => $code]);
    } else {
        sendResponse(false, '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Kích hoạt thất bại. Vui lòng thử lại hoặc liên hệ CSKH.</p>');
    }

} catch (Exception $e) {
    sendResponse(false, '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Lỗi server: ' . htmlspecialchars($e->getMessage()) . '</p>');
} finally {
    if (isset($conn)) $conn->close();
}
?>