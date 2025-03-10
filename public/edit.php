<?php
// admin/edit_record.php
require_once '../includes/db_connect.php';
session_start(); // Đảm bảo rằng session đã được khởi tạo
// Kiểm tra xem có tham số code được truyền không
if (!isset($_GET['code']) || empty($_GET['code'])) {
    header('Location: dashboard.php');
    exit;
}

try {
    $conn = get_db_connection();
    $code = $_GET['code'];
    
    // Truy vấn dữ liệu từ database
    $sql = "SELECT * FROM customerdatabase WHERE Code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<div class='alert alert-danger'>Không tìm thấy dữ liệu!</div>";
        echo "<a href='dashboard.php' class='btn btn-primary'>Quay lại Dashboard</a>";
        require_once 'includes/footer.php';
        exit;
    }
    
    $record = $result->fetch_assoc();
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
    echo "<a href='dashboard.php' class='btn btn-primary'>Quay lại Dashboard</a>";
    require_once 'includes/footer.php';
    exit;
} finally {
    // Đóng kết nối
    if (isset($conn)) {
        close_db_connection($conn);
    }
}

// Danh sách các lựa chọn cho PurchaseLocation
$purchaseLocations = [
    'Website - Emmié by Happyskin',
    'Tiktok - Emmié by Happyskin',
    'Shopee - Emmié by Happyskin',
    'Lazada - Emmié by Happyskin',
    'Store - Emmié by Happyskin'
];

// Danh sách các tỉnh thành
$provinces = [
    "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", "Bắc Ninh", "Bến Tre",
    "Bình Định", "Bình Dương", "Bình Phước", "Bình Thuận", "Cà Mau", "Cần Thơ", "Cao Bằng",
    "Đà Nẵng", "Đắk Lắk", "Đắk Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp", "Gia Lai",
    "Hà Giang", "Hà Nam", "Hà Nội", "Hà Tĩnh", "Hải Dương", "Hải Phòng", "Hậu Giang",
    "Hòa Bình", "Thành phố Hồ Chí Minh", "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum",
    "Lai Châu", "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An",
    "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", "Quảng Bình", "Quảng Nam", "Quảng Ngãi",
    "Quảng Ninh", "Quảng Trị", "Sóc Trăng", "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên",
    "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh", "Tuyên Quang", "Vĩnh Long",
    "Vĩnh Phúc", "Yên Bái"
];

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin - LegitHPS</title>
    <link rel="stylesheet" href="../assets/css/edit_record.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Chỉnh sửa thông tin</h1>
        
        <div class="edit-form">
            <form id="editRecordForm" action="../src/edit_process.php" method="POST">
                <input type="hidden" name="code" value="<?php echo htmlspecialchars($record['Code']); ?>">
                
                <div class="form-group">
                    <label for="SerialNumber">SerialNumber:</label>
                    <input type="text" id="SerialNumber" name="SerialNumber" value="<?php echo htmlspecialchars($record['SerialNumber']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="Code">Code:</label>
                    <input type="text" id="Code" name="Code" value="<?php echo htmlspecialchars($record['Code']); ?>" readonly>
                    <small>Mã code không thể thay đổi</small>
                </div>
                
                <div class="form-group">
                    <label for="CustomerName">CustomerName:</label>
                    <input type="text" id="CustomerName" name="CustomerName" value="<?php echo htmlspecialchars($record['CustomerName']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="PhoneNumber">PhoneNumber:</label>
                    <input type="text" id="PhoneNumber" name="PhoneNumber" value="<?php echo htmlspecialchars($record['PhoneNumber']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="Email">Email:</label>
                    <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($record['Email']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="PurchaseLocation">PurchaseLocation:</label>
                    <select id="PurchaseLocation" name="PurchaseLocation" class="select2">
                        <option value="">-- Chọn nơi mua hàng --</option>
                        <?php foreach ($purchaseLocations as $location): ?>
                            <option value="<?php echo htmlspecialchars($location); ?>" <?php echo ($record['PurchaseLocation'] == $location) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($location); ?>
                            </option>
                        <?php endforeach; ?>
                        <!-- Thêm tùy chọn "Khác" để người dùng có thể nhập giá trị khác -->
                        <option value="other" <?php echo (!in_array($record['PurchaseLocation'], $purchaseLocations) && !empty($record['PurchaseLocation'])) ? 'selected' : ''; ?>>Khác</option>
                    </select>
                    <!-- Hiển thị input khi chọn "Khác" -->
                    <div id="otherPurchaseLocation" style="display: <?php echo (!in_array($record['PurchaseLocation'], $purchaseLocations) && !empty($record['PurchaseLocation'])) ? 'block' : 'none'; ?>; margin-top: 10px;">
                        <input type="text" id="customPurchaseLocation" name="customPurchaseLocation" placeholder="Nhập nơi mua hàng khác" value="<?php echo (!in_array($record['PurchaseLocation'], $purchaseLocations) && !empty($record['PurchaseLocation'])) ? htmlspecialchars($record['PurchaseLocation']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="CityProvince">CityProvince:</label>
                    <select id="CityProvince" name="CityProvince" class="select2">
                        <option value="">-- Chọn tỉnh/thành phố --</option>
                        <?php foreach ($provinces as $province): ?>
                            <option value="<?php echo htmlspecialchars($province); ?>" <?php echo ($record['CityProvince'] == $province) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($province); ?>
                            </option>
                        <?php endforeach; ?>
                        <!-- Thêm tùy chọn "Khác" để người dùng có thể nhập giá trị khác -->
                        <option value="other" <?php echo (!in_array($record['CityProvince'], $provinces) && !empty($record['CityProvince'])) ? 'selected' : ''; ?>>Khác</option>
                    </select>
                    <!-- Hiển thị input khi chọn "Khác" -->
                    <div id="otherCityProvince" style="display: <?php echo (!in_array($record['CityProvince'], $provinces) && !empty($record['CityProvince'])) ? 'block' : 'none'; ?>; margin-top: 10px;">
                        <input type="text" id="customCityProvince" name="customCityProvince" placeholder="Nhập tỉnh/thành phố khác" value="<?php echo (!in_array($record['CityProvince'], $provinces) && !empty($record['CityProvince'])) ? htmlspecialchars($record['CityProvince']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="IsChecked">IsChecked:</label>
                    <select id="IsChecked" name="IsChecked">
                        <option value="0" <?php echo $record['IsChecked'] == 0 ? 'selected' : ''; ?>>Chưa kích hoạt</option>
                        <option value="1" <?php echo $record['IsChecked'] == 1 ? 'selected' : ''; ?>>Đã kích hoạt</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="CheckIP">CheckIP:</label>
                    <input type="text" id="CheckIP" name="CheckIP" value="<?php echo htmlspecialchars($record['CheckIP']); ?>" readonly>
                    <small>IP không thể thay đổi</small>
                </div>
                
                <div class="form-group">
                    <label for="CheckTime">CheckTime:</label>
                    <input type="text" id="CheckTime" name="CheckTime" value="<?php echo htmlspecialchars($record['CheckTime']); ?>" readonly>
                    <small>Thời gian không thể thay đổi</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-save">LƯU</button>
                    <a href="dashboard.php" class="btn-cancel">HỦY</a>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/edit_record.js"></script>
</body>
</html>