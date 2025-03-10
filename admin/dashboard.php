<?php
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require '../src/auth.php'; // Kiểm tra quyền truy cập
?>
<?php
    require '../public/header_admin.php';
?>
    <div class="container">
        <h2>Dashboard Quản Lý</h2>
        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-button" data-tab="checked-data">Dữ Liệu Kiểm Tra</button>
                <button class="tab-button" data-tab="import-codes">Import Mã Kiểm Tra</button>
                <button class="tab-button" data-tab="account-management">Quản Lý Tài Khoản</button>
            </div>
            <div class="tab-content">
                <div id="checked-data" class="tab-pane active">
                    <h3>Dữ Liệu Kiểm Tra</h3>
                    <!-- Nội dung cho tab Dữ Liệu Kiểm Tra -->
                    <?php require '../public/showcode.php'; ?>
                </div>
                <div id="import-codes" class="tab-pane">
                    <h3>Import Mã Kiểm Tra</h3>
                    <!-- Nội dung cho tab Import Mã Kiểm Tra -->
                    <?php require '../public/importcode.php'; ?>
                </div>
                <div id="account-management" class="tab-pane">
                    <h3>Quản Lý Tài Khoản</h3>
                    <!-- Nội dung cho tab Quản Lý Tài Khoản -->
                    <?php require '../public/account_management.php'; ?>
                </div>
                </div>
        </div>
        </div>
    </div>
</body>
</html>