<form id="activateForm">
            <div class="form-group">
                <label for="customer_name">Tên Khách Hàng:</label>
                <input type="text" id="customer_name" name="customer_name" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Số Điện Thoại:</label>
                <input type="text" id="phone_number" name="phone_number" pattern="0[0-9]{9}" title="Số điện thoại VN: 10 chữ số, bắt đầu bằng 0" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="purchase_location">Nơi Mua Sản Phẩm:</label>
                <select id="purchase_location" name="purchase_location" required>
                    <?php
                    $purchase_options = ['Website - Emmié by Happyskin', 'Tiktok - Emmié by Happyskin', 'Shopee - Emmié by Happyskin', 'Lazada - Emmié by Happyskin', 'Store - Emmié by Happyskin'];
                    foreach ($purchase_options as $option) {
                        echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="city_province">Tỉnh/Thành Phố Sinh Sống:</label>
                <select id="city_province" name="city_province" required>
                    <?php
                    $provinces = ["An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", "Bắc Ninh", "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", "Bình Thuận", "Cà Mau", "Cần Thơ", "Cao Bằng", "Đà Nẵng", "Đắk Lắk", "Đắk Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp", "Gia Lai", "Hà Giang", "Hà Nam", "Hà Nội", "Hà Tĩnh", "Hải Dương", "Hải Phòng", "Hậu Giang", "Hòa Bình", "Thành phố Hồ Chí Minh", "Hưng Yên", "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu", "Lâm Đồng", "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An", "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", "Quảng Bình", "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng", "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên", "Thanh Hóa", "Thừa Thiên Huế", "Tiền Giang", "Trà Vinh", "Tuyên Quang", "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"];
                    foreach ($provinces as $province) {
                        echo '<option value="' . htmlspecialchars($province) . '">' . htmlspecialchars($province) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="code">CODE:</label>
                <input type="text" id="code" name="code" value="<?php echo isset($_GET['code']) ? htmlspecialchars($_GET['code']) : ''; ?>" required>
                <small>Nhập mã CODE trên sản phẩm</small>
            </div>
            <button type="submit" class="activate-btn">Kích Hoạt</button>
        </form>
        <div id="resultBox" class="result-box" style="display: none;"></div>
<script>
    window.csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';
</script>