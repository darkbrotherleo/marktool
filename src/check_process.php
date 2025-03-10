<?php
// src/check_process.php
session_start();
require_once '../includes/db_connect.php';

// Tạo CSRF token nếu chưa có
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Kiểm tra CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Yêu cầu không hợp lệ. Vui lòng thử lại.'
    ]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = get_db_connection();
        
        if (!$conn) {
            throw new Exception("Không thể kết nối đến cơ sở dữ liệu");
        }
        
        $code = $conn->real_escape_string($_POST['code']);
        
        // Truy vấn toàn bộ thông tin sản phẩm
        $sql = "SELECT * FROM customerdatabase WHERE Code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        $response = [
            'success' => false,
            'message' => '',
            'buttonText' => '',
            'buttonLink' => '',
            'error' => false,
            'product' => null
        ];

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            
            // Định dạng lại thời gian nếu có
            if (!empty($product['CheckTime']) && $product['CheckTime'] !== '0000-00-00 00:00:00') {
                $checkTime = new DateTime($product['CheckTime']);
                $checkTime->format('d/m/Y H:i:s');
            }
            
            // Thêm thông tin sản phẩm vào response
            $response['product'] = $product;
            
            if ($product['IsChecked'] == 0) {
                // Hiển thị thông báo và nút chuyển đến activate.php
                $response['message'] = "CODE chưa kích hoạt.";
                $response['buttonText'] = "Kích Hoạt Ngay";
                $response['buttonLink'] = '../src/activate_process.php?code=' . urlencode($code);
            } else {
                // Thông báo thành công với màu xanh lá ngọc
                $response['success'] = true;
                $response['message'] = "THÔNG BÁO: Tiến hành kiểm tra sản phẩm chính hãng thành công, sản phẩm bạn mua là sản phẩm chính hãng của Emmié by HappySkin.";
            }
        } else {
            // Không tìm thấy CODE, hiển thị thông báo lỗi
            $response['error'] = true;
            $response['message'] = "THÔNG BÁO: Mã vừa nhập không có trên hệ thống, liên hệ với kỹ thuật để được giải quyết.";
        }

        $stmt->close();
        $conn->close();
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => true,
            'message' => 'Lỗi hệ thống: ' . $e->getMessage()
        ]);
        exit();
    }
}

// Nếu không phải POST request
header('Content-Type: application/json');
echo json_encode([
    'success' => false,
    'message' => 'Phương thức không được hỗ trợ.'
]);
exit();
?>
