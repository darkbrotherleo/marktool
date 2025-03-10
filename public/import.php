<?php session_start(); // Đảm bảo rằng session đã được khởi tạo ?>
<!-- public/import.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import CODE - LegitHPS</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <meta name="csrf-token" content="<?php htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <div class="container">
        <h2>Import CODE từ CSV</h2>
        <p class="import-note">File CSV cần có 2 cột: SerialNumber,Code. Dữ liệu bắt đầu từ dòng 3. Import tối đa 5000 dòng/lần.</p>
        <form method="POST" action="../src/import_process.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="csv_file">Chọn file CSV:</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
            </div>
            <button type="submit">Import</button>
        </form>

        <?php
        if (isset($_GET['result'])) {
            echo '<div class="result">';
            echo $_GET['result'];
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>