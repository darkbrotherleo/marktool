<?php
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Kiểm tra biến phiên
if (!isset($_SESSION['id'], $_SESSION['email'], $_SESSION['role'])) {
    die("Vui lòng đăng nhập để tiếp tục.");
}

require_once '../includes/db_connect.php';
$conn = get_db_connection();

// Số dòng trên mỗi trang
$rowsPerPage = 10;

// Lấy trang hiện tại (mặc định là 1)
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $rowsPerPage;

// Truy vấn tổng số bản ghi
$totalSql = "SELECT COUNT(*) AS total FROM users"; // Thay đổi tên bảng nếu cần
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRow / $rowsPerPage);

// Truy vấn dữ liệu cho trang hiện tại
$sql = "SELECT * FROM users LIMIT $offset, $rowsPerPage"; // Thay đổi tên bảng nếu cần
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tài Khoản</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="container">
        <h2>Danh Sách Tài Khoản</h2>
        <div class="export-options">
            <select id="exportOption">
                <option value="">Chọn để xuất dữ liệu tài khoản</option>
                <option value="all">Xuất Toàn Bộ Tài Khoản</option>
                <option value="active">Xuất Tài Khoản Đã Kích Hoạt</option>
                <option value="inactive">Xuất Tài Khoản Chưa Kích Hoạt</option>
            </select>
            <button id="exportCsvBtn" class="export-btn" disabled>Xuất Dữ Liệu USER</button>
        </div>

        <?php
        if ($result->num_rows > 0) {
            echo '<table class="data-table">';
            echo '<tr><th>ID</th><th>Email</th><th>Vai Trò</th><th>Trạng Thái</th><th>Hành Động</th></tr>';
            while ($user = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($user['id']) . '</td>';
                echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                echo '<td>' . htmlspecialchars($user['role']) . '</td>';
                echo '<td>' . ($user['status'] == 1 ? 'Kích hoạt' : 'Chưa kích hoạt') . '</td>';
                echo '<td><a href="../public/edit_user.php?email=' . urlencode($user['email']) . '" class="edit-link">Chỉnh sửa</a></td>';
                echo '</tr>';
            }
            echo '</table>';

            // Hiển thị phân trang
            echo '<div class="pagination">';
            echo '<span class="pagination-info">' . number_format($totalRow) . ' tài khoản trên ' . number_format($totalPages) . '</span>';
            // Nút First (<<)
            if ($currentPage > 1) {
                echo '<a href="?page=1" class="pagination-btn">«</a>';
            } else {
                echo '<span class="pagination-btn disabled">«</span>';
            }
            // Nút Previous (<)
            if ($currentPage > 1) {
                echo '<a href="?page=' . ($currentPage - 1) . '" class="pagination-btn"><</a>';
            } else {
                echo '<span class="pagination-btn disabled"><</span>';
            }
            // Số trang (hiển thị 5 trang xung quanh trang hiện tại)
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            for ($i = $startPage; $i <= $endPage; $i++) {
                echo '<a href="?page=' . $i . '" class="pagination-btn' . ($i == $currentPage ? ' active' : '') . '">' . $i . '</a>';
            }
            // Nút Next (>)
            if ($currentPage < $totalPages) {
                echo '<a href="?page=' . ($currentPage + 1) . '" class="pagination-btn">></a>';
            } else {
                echo '<span class="pagination-btn disabled">></span>';
            }
            // Nút Last (>>)
            if ($currentPage < $totalPages) {
                echo '<a href="?page=' . $totalPages . '" class="pagination-btn">»</a>';
            } else {
                echo '<span class="pagination-btn disabled">»</span>';
            }
            echo '</div>'; // Kết thúc div.pagination
        } else {
            echo '<p>Chưa có tài khoản nào được tìm thấy.</p>';
        }
        $conn->close();
        ?>
    </div>
</body>
</html>