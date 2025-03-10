<?php

require '../vendor/autoload.php';
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Tạo đối tượng Dotenv và tải file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); // Điều chỉnh đường dẫn nếu cần
$dotenv->load();

function get_db_connection() {
    // Kết nối đến MySQL
    $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Hàm đóng kết nối
function close_db_connection($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>