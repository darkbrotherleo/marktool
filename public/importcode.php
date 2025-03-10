
<p class="import-note"><a href="..\public\Template_Import_Code_HPS.csv" class="download-link" download>Tải Mẫu Template_Code.csv</a></p>
    <form method="POST" action="../src/import_process.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="csv_file">Chọn file CSV:</label>
            <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
        </div>
            <button type="submit">Import</button>
    </form>
    <div id="progress" style="display: none; margin-top: 20px;">
        <progress id="progressBar" value="0" max="100" style="width: 100%;"></progress>
        <span id="progressText">0%</span>
    </div>
    <form action="../src/truncate_process.php" method="POST">
        <button type="submit" style="padding: 10px 20px; font-size: 16px; color: white; background-color: red; border: none; border-radius: 5px; cursor: pointer;">
        XÓA DỮ LIỆU
        </button>
    </form>
<?php
    if (isset($_GET['result'])) {
        echo '<div class="result">' . $_GET['result'] . '</div>';
    }
?>