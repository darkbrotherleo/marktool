document.addEventListener('DOMContentLoaded', function() {
    const checkForm = document.getElementById('checkForm');
    if (checkForm) {
        checkForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn chặn form gửi thông thường
            
            const code = document.getElementById('code').value.trim();
            if (!code) {
                alert('Vui lòng nhập mã CODE');
                return;
            }

            const formData = new FormData(this);
            
            // Lấy CSRF token từ meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            formData.append('csrf_token', csrfToken);

            // Debug: Kiểm tra giá trị của mã CODE và CSRF token
            console.log('Code:', code);
            console.log('CSRF Token:', csrfToken);

            // Hiển thị trạng thái đang xử lý
            const resultBox = document.getElementById('resultBox');
            resultBox.style.display = 'block';
            resultBox.innerHTML = '<p>Đang kiểm tra, vui lòng đợi...</p>';

            fetch('../src/check_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success || data.product) {
                    // Hiển thị thông tin sản phẩm dưới dạng bảng
                    let html = '<p class="success-message">' + data.message + '</p>';
                    
                    if (data.product) {
                        html += '<table class="result-table">';
                        html += '<tr><th>Thông tin</th><th>Chi tiết</th></tr>';
                        html += '<tr><td>Mã CODE</td><td>' + (data.product.Code || 'N/A') + '</td></tr>';
                        html += '<tr><td>Serial Number</td><td>' + (data.product.SerialNumber || 'N/A') + '</td></tr>';
                        html += '<tr><td>Tên khách hàng</td><td>' + (data.product.CustomerName || 'Chưa kích hoạt') + '</td></tr>';
                        html += '<tr><td>Số điện thoại</td><td>' + (data.product.PhoneNumber || 'Chưa kích hoạt') + '</td></tr>';
                        html += '<tr><td>Email</td><td>' + (data.product.Email || 'Chưa kích hoạt') + '</td></tr>';
                        html += '<tr><td>Nơi mua hàng</td><td>' + (data.product.PurchaseLocation || 'Chưa kích hoạt') + '</td></tr>';
                        html += '<tr><td>Tỉnh/Thành phố</td><td>' + (data.product.CityProvince || 'Chưa kích hoạt') + '</td></tr>';
                        html += '<tr><td>Trạng thái kích hoạt</td><td>' + 
                                (data.product.IsChecked == 1 ? 
                                '<span class="status-active">Đã kích hoạt</span>' : 
                                '<span class="status-inactive">Chưa kích hoạt</span>') + 
                                '</td></tr>';
                        
                        if (data.product.CheckTime && data.product.CheckTime !== '0000-00-00 00:00:00') {
                            html += '<tr><td>Thời gian kích hoạt</td><td>' + data.product.CheckTime + '</td></tr>';
                        }
                        
                        if (data.product.CheckIP) {
                            html += '<tr><td>IP kích hoạt</td><td>' + data.product.CheckIP + '</td></tr>';
                        }
                        
                        html += '</table>';
                    }
                    
                    resultBox.innerHTML = html;
                } else if (data.error) {
                    // Hiển thị thông báo lỗi
                    resultBox.innerHTML = '<p class="error-message">' + data.message + '</p>';
                } else {
                    // Hiển thị thông báo khác
                    resultBox.innerHTML = '<p class="error-message">' + data.message + '</p>';
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                resultBox.innerHTML = '<p class="error-message">Lỗi khi kiểm tra mã. Vui lòng thử lại.</p>';
            });
        });
    }
});