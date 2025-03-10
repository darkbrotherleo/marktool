<?php
// src/export_all_csv.php
require_once '../includes/db_connect.php';
session_start();
$conn = get_db_connection();

if ($conn) {
    // Truy vấn tất cả dữ liệu từ CustomerDatabase
    $sql = "SELECT SerialNumber, Code, CustomerName, PhoneNumber, Email, PurchaseLocation, CityProvince, IsChecked, CheckIP, CheckTime FROM CustomerDatabase";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Thiết lập header để tải file CSV với UTF-8 và BOM
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="customer_database_all.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Mở output buffer và thêm BOM để hỗ trợ UTF-8 trong Excel
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // Thêm BOM

        // Ghi header (tiêu đề cột) vào CSV
        $headers = ['Serial Number', 'Code', 'Tên Khách Hàng', 'Số Điện Thoại', 'Email', 'Nơi Mua Sản Phẩm', 'Tỉnh/Thành Phố', 'Trạng Thái', 'CheckIP', 'Thời Gian Kiểm Tra'];
        fputcsv($output, $headers);

        // Ghi dữ liệu từng dòng
        while ($row = $result->fetch_assoc()) {
            // Định dạng cột CheckTime nếu có
            $row['CheckTime'] = !empty($row['CheckTime']) && $row['CheckTime'] !== '0000-00-00 00:00:00' 
                ? date('d/m/Y H:i', strtotime($row['CheckTime'])) 
                : '';
            // Chuyển IsChecked thành "Chưa Kích Hoạt" hoặc "Đã Kích Hoạt"
            $row['IsChecked'] = $row['IsChecked'] == 0 ? 'Chưa Kích Hoạt' : 'Đã Kích Hoạt';
            fputcsv($output, [
                $row['SerialNumber'],
                $row['Code'],
                $row['CustomerName'],
                $row['PhoneNumber'],
                $row['Email'],
                $row['PurchaseLocation'],
                $row['CityProvince'],
                $row['IsChecked'],
                $row['CheckIP'],
                $row['CheckTime']
            ]);
        }

        fclose($output);
    } else {
        // Nếu không có dữ liệu, trả về lỗi JSON
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'message' => 'Không có dữ liệu để xuất CSV.'
        ]);
    }

    $conn->close();
    exit();
} else {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'message' => 'Kết nối cơ sở dữ liệu thất bại.'
    ]);
    exit();
}
?>