
                    <div class="export-options">
                        <select id="exportOption">
                            <option value="">Chọn để xuất dữ liệu CSV</option>
                            <option value="all">Xuất Toàn Bộ CODE</option>
                            <option value="active">Xuất CODE Đã Kích Hoạt</option>
                            <option value="inactive">Xuất CODE Chưa Kích Hoạt</option>
                        </select>
                        <button id="exportCsvBtn" class="export-btn" disabled>Xuất Dữ Liệu CSV</button>
                    </div>
                    <?php
                    require_once '../includes/db_connect.php';
                    $conn = get_db_connection();

                    // Số dòng trên mỗi trang
                    $rowsPerPage = 10;
                    
                    // Lấy trang hiện tại (mặc định là 1)
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $rowsPerPage;

                    // Truy vấn tổng số bản ghi
                    $totalSql = "SELECT COUNT(*) AS total FROM customerdatabase";
                    $totalResult = $conn->query($totalSql);
                    $totalRow = $totalResult->fetch_assoc()['total'];
                    $totalPages = ceil($totalRow / $rowsPerPage);

                    // Truy vấn dữ liệu cho trang hiện tại
                    $sql = "SELECT * FROM customerdatabase LIMIT $offset, $rowsPerPage";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo '<table class="data-table">';
                        echo '<tr><th>Serial Number</th><th>Code</th><th>Tên Khách Hàng</th><th>Số Điện Thoại</th><th>Email</th><th>Thời Gian Kiểm Tra</th><th>Status</th></tr>';
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['SerialNumber']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Code']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['CustomerName']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['PhoneNumber']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['Email']) . '</td>';
                            echo '<td>';
                            if (!empty($row['CheckTime']) && $row['CheckTime'] !== '0000-00-00 00:00:00') {
                                echo date('d/m/Y H:i', strtotime($row['CheckTime']));
                            }
                            echo '</td>';
                            echo '<td>';
                            if ($row['IsChecked'] == 0) {
                                echo '<span class="status-unactivated">X</span>';
                                echo '<span class="status-unactivated"> - </span>';
                                echo '<span class="status-unactivated"><a href="../public/edit.php?code=' . urlencode($row['Code']) . '" class="fix-link">FIX</a></span>';
                            } else {
                                echo '<span class="status-activated">V</span>';
                                echo '<span class="status-unactivated"> - </span>';
                                echo '<span class="status-unactivated"><a href="../public/edit.php?code=' . urlencode($row['Code']) . '" class="fix-link">FIX</a></span>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';

                        // Hiển thị phân trang
                        echo '<div class="pagination">';
                        echo '<span class="pagination-info">' . number_format($totalRow) . ' mục trên ' . number_format($totalPages) . '</span>';
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
                        echo '</div>';
                    } else {
                        echo '<p>Chưa có dữ liệu kiểm tra.</p>';
                    }
                    $conn->close();
                    ?>
