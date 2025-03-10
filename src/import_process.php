<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Không có file CSV được tải lên.']);
    exit;
}

// Lấy thư mục tạm động
$temp_dir = sys_get_temp_dir();
$safe_file = $temp_dir . DIRECTORY_SEPARATOR . 'import_' . uniqid() . '.csv';
$upload_file = $_FILES['csv_file']['tmp_name'];

if (!move_uploaded_file($upload_file, $safe_file)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không thể di chuyển file CSV.']);
    exit;
}
$file = $safe_file;

$offset = (int)($_POST['offset'] ?? 0);
$limit = (int)($_POST['limit'] ?? 10000); // Giữ limit 10000

if (!file_exists($file)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'File tạm không tồn tại: ' . $file]);
    exit;
}

$conn = get_db_connection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database.']);
    exit;
}

// Đếm tổng số dòng
$handle = fopen($file, 'r');
if (!$handle) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không thể mở file CSV.']);
    exit;
}

$total_lines = 0;
while (!feof($handle)) {
    if (fgets($handle) !== false) $total_lines++;
}
rewind($handle);
$data_lines = max(0, $total_lines - 2); // Bỏ qua 2 dòng đầu
error_log("Total lines: $total_lines, Data lines: $data_lines, Offset: $offset, Limit: $limit");

try {
    $conn->begin_transaction();

    // Tạo bảng tạm
    if (!$conn->query("DROP TEMPORARY TABLE IF EXISTS temp_import")) {
        throw new Exception('Lỗi khi xóa bảng tạm: ' . $conn->error);
    }
    if (!$conn->query("CREATE TEMPORARY TABLE temp_import (
        SerialNumber VARCHAR(50),
        Code VARCHAR(50),
        IsChecked TINYINT DEFAULT 0
    )")) {
        throw new Exception('Lỗi khi tạo bảng tạm: ' . $conn->error);
    }

    // Chuẩn hóa đường dẫn file cho MySQL
    $file_for_mysql = str_replace('\\', '\\\\', $file);
    $query = "LOAD DATA LOCAL INFILE '$file_for_mysql'
              INTO TABLE temp_import
              FIELDS TERMINATED BY ','
              ENCLOSED BY '\"'
              LINES TERMINATED BY '\n'
              IGNORE 2 LINES
              (SerialNumber, Code, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy)";
    if (!$conn->query($query)) {
        throw new Exception('Lỗi khi import vào bảng tạm: ' . $conn->error);
    }
    error_log("Loaded data into temp_import");

    // Kiểm tra số dòng thực tế trong bảng tạm
    $result = $conn->query("SELECT COUNT(*) FROM temp_import");
    $temp_count = $result->fetch_row()[0];
    error_log("Total rows in temp_import: $temp_count");

    $start = $offset;
    $end = min($offset + $limit, $data_lines);
    error_log("Processing from line $start to $end");

    // Chuyển dữ liệu từ bảng tạm sang bảng chính
    $transfer_query = "
        INSERT INTO customerdatabase (SerialNumber, Code, IsChecked)
        SELECT t.SerialNumber, t.Code, t.IsChecked
        FROM temp_import t
        LEFT JOIN customerdatabase c ON t.Code = c.Code
        WHERE c.Code IS NULL
        LIMIT $start, $limit
    ";
    if (!$conn->query($transfer_query)) {
        throw new Exception('Lỗi khi chuyển dữ liệu từ bảng tạm: ' . $conn->error);
    }
    $imported = $conn->affected_rows;
    error_log("Imported $imported rows from offset $offset");

    if (!$conn->query("DROP TEMPORARY TABLE temp_import")) {
        throw new Exception('Lỗi khi xóa bảng tạm sau import: ' . $conn->error);
    }

    $conn->commit();
    fclose($handle);
    unlink($file);
    $conn->close();

    $next_offset = $offset + $imported;
    $response = [
        'success' => true,
        'message' => "Đã import: $imported dòng. Tổng số dòng dữ liệu: $data_lines (bù 2 dòng header, tổng file: $total_lines dòng)",
        'imported' => $imported,
        'total_data_lines' => $data_lines,
        'next_offset' => $next_offset < $data_lines ? $next_offset : null
    ];

    if ($next_offset >= $data_lines) {
        $response['redirect'] = '../admin/dashboard.php';
    }

    error_log("Response: " . json_encode($response));
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    $conn->rollback();
    if (isset($handle)) fclose($handle);
    if (file_exists($file)) unlink($file);
    $conn->close();
    error_log("Import error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi khi import: ' . $e->getMessage()]);
}
?>