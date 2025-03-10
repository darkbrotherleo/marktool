document.addEventListener('DOMContentLoaded', () => {
    // Lấy CSRF token từ meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    if (!csrfToken) {
        console.warn('CSRF token không được tìm thấy. Vui lòng kiểm tra cấu hình PHP.');
    }

    // Các element thường dùng
    const elements = {
        exportOption: document.getElementById('exportOption'),
        exportBtn: document.getElementById('exportCsvBtn'),
        tabButtons: document.querySelectorAll('.tab-button'),
        tabPanes: document.querySelectorAll('.tab-pane'),
        bannerForm: document.getElementById('bannerForm'),
        deleteButtons: document.querySelectorAll('.delete-btn'),
        setDefaultButtons: document.querySelectorAll('.set-default-btn'),
        inputForm: document.getElementById('inputForm'),
        activateForm: document.getElementById('activateForm'),
        resultBox: document.getElementById('resultBox'),
        importFile: document.getElementById('importFile'), // Thêm element cho file import
        importButton: document.getElementById('importButton'), // Thêm nút import
        progressBar: document.getElementById('progressBar'),
        progressText: document.getElementById('progressText'),
        progressDiv: document.getElementById('progress')
    };

    // Hàm tải file (CSV, v.v.)
    function downloadFile(url, filename) {
        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Lỗi khi tải file: ' + res.status);
                return res.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Lỗi tải file:', error);
                alert('Không thể tải file. Vui lòng thử lại.');
            });
    }

    // Hàm hiển thị kết quả trong resultBox
    function showResult(data) {
        if (!elements.resultBox) {
            console.error('Không tìm thấy resultBox.');
            alert('Lỗi khi hiển thị kết quả. Vui lòng thử lại.');
            return;
        }
        elements.resultBox.style.display = 'block';
        elements.resultBox.innerHTML = `
            <p class="${data.success ? 'success-message' : 'error-message'}" style="color: ${data.success ? '#28a745' : '#dc3545'}; font-weight: 600;">
                ${data.message || 'Không có thông báo.'}
            </p>
            ${data.buttonLink ? `<a href="${data.buttonLink}" class="back-button">${data.buttonText || 'Quay lại'}</a>` : ''}
        `;
    }

    // Lấy tab hiện tại từ localStorage
    const currentTab = localStorage.getItem('currentTab') || 'checked-data';

    // Hiển thị tab hiện tại
    document.querySelectorAll('.tab-pane').forEach(tab => {
        tab.classList.remove('active');
    });
    document.getElementById(currentTab).classList.add('active');

    // Thêm sự kiện click cho các tab
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');

            // Ẩn tất cả các tab
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.remove('active');
            });

            // Hiển thị tab được chọn
            document.getElementById(tabId).classList.add('active');

            // Lưu tab hiện tại vào localStorage
            localStorage.setItem('currentTab', tabId);
        });
    });

    // Gắn sự kiện load trang
    window.addEventListener('load', () => {
        // Lấy trạng thái tab hiện tại từ localStorage
        const storedTab = localStorage.getItem('currentTab');
        
        // Chuyển đến tab hiện tại
        if (storedTab) {
            const tabButton = document.querySelector(`.tab-button[data-tab="${storedTab}"]`);
            if (tabButton) {
                tabButton.click();
            }
        } else {
            const tabButton = document.querySelector(`.tab-button[data-tab="${currentTab}"]`);
            if (tabButton) {
                tabButton.click();
            }
        }
    });

    // Hàm xử lý xuất CSV/Excel
    function handleExport() {
        if (!elements.exportOption || !elements.exportBtn) return;
        elements.exportOption.addEventListener('change', () => {
            const option = elements.exportOption.value;
            elements.exportBtn.disabled = !option;
            elements.exportBtn.dataset.type = option || '';
        });
        elements.exportBtn.addEventListener('click', () => {
            const exportType = elements.exportBtn.dataset.type;
            if (!exportType) return;
            const urls = {
                all: '../src/export_all_csv.php',
                active: '../src/export_active_csv.php',
                inactive: '../src/export_inactive_csv.php'
            };
            downloadFile(urls[exportType], `customer_database_${exportType}.csv`);
        });
    }

    // Hàm xử lý tabs, cố định ở "Import Mã Kiểm Tra" (data-tab="import")
    function handleTabs() {
        if (!elements.tabButtons || !elements.tabPanes) return;

        // [BỔ SUNG]: Cố định tab "import" (tương ứng "Import Mã Kiểm Tra")
        const fixedTab = 'import';

        // [BỔ SUNG]: Ngăn chặn chuyển tab khác, chỉ cho phép "import"
        elements.tabButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                if (button.dataset.tab !== fixedTab) {
                    event.preventDefault(); // Ngăn chặn chuyển tab nếu không phải "import"
                    return;
                }
                elements.tabButtons.forEach(btn => btn.classList.remove('active'));
                elements.tabPanes.forEach(pane => pane.classList.remove('active'));
                button.classList.add('active');
                const tabPane = document.getElementById(button.dataset.tab);
                if (tabPane) tabPane.classList.add('active');
            });
        });

        // [BỔ SUNG]: Luôn active tab "import" khi load trang (sau F5 hoặc reload)
        const tabToActivate = document.querySelector(`.tab-button[data-tab="${fixedTab}"]`);
        if (tabToActivate) {
            elements.tabButtons.forEach(btn => btn.classList.remove('active'));
            elements.tabPanes.forEach(pane => pane.classList.remove('active'));
            tabToActivate.classList.add('active');
            const tabPane = document.getElementById(fixedTab);
            if (tabPane) tabPane.classList.add('active');
        }
    }

    // Hàm hiển thị kết quả trong resultBox (đã có, giữ nguyên)
    function showResult(data) {
        if (!elements.resultBox) {
            console.error('Không tìm thấy resultBox.');
            alert('Lỗi khi hiển thị kết quả. Vui lòng thử lại.');
            return;
        }
        elements.resultBox.style.display = 'block';
        elements.resultBox.innerHTML = `
            <p class="${data.success ? 'success-message' : 'error-message'}" style="color: ${data.success ? '#28a745' : '#dc3545'}; font-weight: 600;">
                ${data.message || 'Không có thông báo.'}
            </p>
            ${data.buttonLink ? `<a href="${data.buttonLink}" class="back-button">${data.buttonText || 'Quay lại'}</a>` : ''}
        `;
    }

    // Hàm xử lý form nhập liệu
    function handleInputForm() {
        if (!elements.inputForm) return;
        elements.inputForm.addEventListener('submit', async e => {
            e.preventDefault();
            try {
                const formData = new FormData(elements.inputForm);
                const response = await fetch('../src/check_process.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) throw new Error('Lỗi server: ' + response.status);
                const data = await response.json();
                showResult(data);
            } catch (error) {
                console.error('Lỗi:', error);
                alert('Lỗi khi gửi dữ liệu. Vui lòng thử lại.');
            }
        });
    }

    // Hàm xử lý form kích hoạt (activate_process.php)
    function handleActivateForm() {
        if (!elements.activateForm) return;
        elements.activateForm.addEventListener('submit', async e => {
            e.preventDefault();
            const code = elements.activateForm.code?.value.trim();
            if (!code || code.length !== 6 || !/^\d+$/.test(code)) {
                showResult({
                    success: false,
                    message: 'Mã CODE phải gồm 6 chữ số!'
                });
                return;
            }
            const formData = {
                customer_name: elements.activateForm.customer_name?.value || '',
                phone_number: elements.activateForm.phone_number?.value || '',
                email: elements.activateForm.email?.value || '',
                purchase_location: elements.activateForm.purchase_location?.value || '',
                city_province: elements.activateForm.city_province?.value || '',
                code: code,
                csrf_token: csrfToken
            };
            try {
                const response = await fetch('../src/activate_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
                if (!response.ok) throw new Error('Lỗi server: ' + response.status);
                const data = await response.json();
                if (data.success === true) {
                    showResult(data);
                    elements.activateForm.reset();
                } else {
                    showResult({ 
                        success: false, 
                        message: data.message || 'Mã CODE đã được kích hoạt trước đó.' 
                    });
                }
            } catch (error) {
                console.error('Lỗi:', error);
                showResult({
                    success: false,
                    message: 'Lỗi kết nối server. Vui lòng thử lại.'
                });
            }
        });
    }

    // Hàm validate file CSV
    function validateFile(input) {
        const file = input.files[0];
        const fileType = file.name.split('.').pop().toLowerCase();
        const supportedTypes = ['csv'];
        if (supportedTypes.includes(fileType)) {
            elements.importButton.disabled = false;
        } else {
            alert('Vui lòng chọn file CSV.');
            elements.importButton.disabled = true;
            input.value = '';
        }
    }

    // Hàm import dữ liệu từng phần 20,000 dòng, không gửi tab vì đã cố định
    function importData() {
        if (!elements.importFile || !elements.importButton || !elements.progressBar || !elements.progressText || !elements.progressDiv) {
            console.error('Các element import không được tìm thấy.');
            alert('Lỗi cấu hình giao diện. Vui lòng kiểm tra lại.');
            return;
        }

        const file = elements.importFile.files[0];
        if (!file) {
            alert('Vui lòng chọn file trước khi import.');
            return;
        }

        elements.progressDiv.style.display = 'block';
        elements.progressBar.value = 0;
        elements.progressText.textContent = '0%';

        let offset = 0; // Vị trí bắt đầu
        const limit = 20002; // Số dòng mỗi phần + 2 dòng đầu để bù (tính toán)
        let totalLines = 0; // Tổng số dòng dữ liệu (sẽ lấy từ server)

        function processNextChunk() {
            const formData = new FormData();
            formData.append('csv_file', file);
            formData.append('offset', offset); // Đảm bảo gửi offset
            formData.append('limit', limit - 2); // Gửi limit thực tế (20,000) để import dữ liệu từ dòng 3
            // [BỔ SUNG]: Không gửi tham số tab vì đã cố định tab "import"

            console.log('Gửi request đến:', '../src/import_process.php');
            console.log('File selected:', file, 'Offset:', offset, 'Limit:', limit - 2);
            fetch('../src/import_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Lỗi server: ' + response.status + ' - ' + response.statusText);
                }
                return response.text(); // Đọc response dưới dạng text trước (HTML từ PHP)
            })
            .then(text => {
                try {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(text, 'text/html');
                    const resultElement = doc.querySelector('.progress, h3, p');
                    let message = resultElement ? resultElement.textContent.trim() : 'Không tìm thấy kết quả.';
                    console.log('Response từ server (HTML):', message);

                    // Cập nhật tiến độ dựa trên số dòng đã import
                    const match = message.match(/Đã import: (\d+) dòng/);
                    if (match) {
                        const imported = parseInt(match[1], 10);
                        const totalMatch = message.match(/Tổng số dòng import: (\d+) \(bù 2 dòng đầu, tổng file: (\d+) dòng\)/);
                        totalLines = totalMatch ? parseInt(totalMatch[2], 10) : totalLines || 0; // Sử dụng tổng file (bao gồm 2 dòng đầu)
                        const progress = (offset + imported) / (totalLines - 2) * 100 || 0; // Tính tiến độ dựa trên dữ liệu (không tính 2 dòng đầu)
                        elements.progressBar.value = progress;
                        elements.progressText.textContent = `${Math.round(progress)}%`;
                    }

                    // Kiểm tra xem có cần import phần tiếp theo không
                    const nextOffsetMatch = text.match(/offset=(\d+)&limit=\d+&total=(\d+)/);
                    if (nextOffsetMatch) {
                        offset = parseInt(nextOffsetMatch[1], 10);
                        totalLines = parseInt(nextOffsetMatch[2], 10) + 2; // Bù thêm 2 dòng đầu cho tổng file
                        if (offset < (totalLines - 2)) { // So sánh với dữ liệu (không tính 2 dòng đầu)
                            setTimeout(processNextChunk, 1000); // Chờ 1 giây trước khi gửi phần tiếp theo
                            return;
                        }
                    }

                    // Hoàn thành import
                    elements.progressBar.value = 100;
                    elements.progressText.textContent = '100%';
                    alert('Import toàn bộ file thành công! Tổng số dòng import: ' + (totalLines - 2) + ' (bù 2 dòng đầu, tổng file: ' + totalLines + ' dòng)');

                    // [BỔ SUNG]: Hiển thị kết quả import trên tab "Import Mã Kiểm Tra" mà không chuyển tab
                    showResult({
                        success: true,
                        message: `Hoàn thành import toàn bộ file! Tổng số dòng import: ${totalLines - 2} (bù 2 dòng đầu, tổng file: ${totalLines} dòng).`
                    });
                } catch (e) {
                    throw new Error('Không thể parse response: ' + e.message + ' - Nội dung: ' + text);
                }
            })
            .catch(error => {
                let errorMessage = 'Không xác định lỗi cụ thể.';
                if (error instanceof Error) {
                    errorMessage = error.message || 'Lỗi không có thông điệp cụ thể.';
                } else if (typeof error === 'string') {
                    errorMessage = error;
                } else if (error && typeof error === 'object') {
                    errorMessage = JSON.stringify(error);
                }

                console.error('Lỗi chi tiết:', errorMessage);
                elements.progressBar.value = 100;
                elements.progressText.textContent = '100%';
                alert('Có lỗi xảy ra khi import dữ liệu: ' + errorMessage);

                // [BỔ SUNG]: Hiển thị lỗi trên tab "Import Mã Kiểm Tra" mà không chuyển tab
                showResult({
                    success: false,
                    message: `Có lỗi xảy ra khi import dữ liệu: ${errorMessage}`
                });
            })
            .finally(() => {
                // Không ẩn progressDiv ngay, để người dùng thấy tiến độ hoàn thành
            });
        }

        // Gửi request đầu tiên để lấy tổng số dòng
        const formDataInitial = new FormData();
        formDataInitial.append('csv_file', file);
        formDataInitial.append('offset', 0); // Đảm bảo gửi offset
        formDataInitial.append('limit', 1); // Chỉ lấy 1 dòng dữ liệu để đếm tổng (sau 2 dòng đầu)
        // [BỔ SUNG]: Không gửi tham số tab vì đã cố định

        fetch('../src/import_process.php', {
            method: 'POST',
            body: formDataInitial
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Lỗi server: ' + response.status + ' - ' + response.statusText);
            }
            return response.text();
        })
        .then(text => {
            const totalMatch = text.match(/Tổng số dòng import: (\d+) \(bù 2 dòng đầu, tổng file: (\d+) dòng\)/);
            if (totalMatch) {
                totalLines = parseInt(totalMatch[2], 10); // Sử dụng tổng file (bao gồm 2 dòng đầu)
                console.log('Tổng số dòng dữ liệu (bù 2 dòng đầu, tổng file):', totalLines);
                processNextChunk(); // Bắt đầu import phần đầu tiên
            } else {
                throw new Error('Không thể xác định tổng số dòng trong file CSV.');
            }
        })
        .catch(error => {
            let errorMessage = 'Không xác định lỗi cụ thể.';
            if (error instanceof Error) {
                errorMessage = error.message || 'Lỗi không có thông điệp cụ thể.';
            }
            console.error('Lỗi chi tiết khi lấy tổng số dòng:', errorMessage);
            elements.progressBar.value = 100;
            elements.progressText.textContent = '100%';
            alert('Có lỗi xảy ra khi xác định tổng số dòng: ' + errorMessage);

            // [BỔ SUNG]: Hiển thị lỗi trên tab "Import Mã Kiểm Tra" mà không chuyển tab
            showResult({
                success: false,
                message: `Có lỗi xảy ra khi xác định tổng số dòng: ${errorMessage}`
            });
        });
    }

    // Hàm chuyển đến tab trước đó đang hiển thị
    function returnToCurrentTab() {
        const storedTab = localStorage.getItem('currentTab');
        if (storedTab) {
            const tabButton = document.querySelector(`.tab-button[data-tab="${storedTab}"]`);
            if (tabButton) {
                tabButton.click();
            }
        }
    }

    // [BỔ SUNG]: Gắn sự kiện validate và import
    if (elements.importFile && elements.importButton) {
        elements.importFile.addEventListener('change', (e) => validateFile(e.target));
        elements.importButton.addEventListener('click', importData);
    }

    // Khởi chạy các chức năng
    handleExport();
    handleTabs();
    handleInputForm();
    handleActivateForm();

    // Xử lý form banner
    if (elements.bannerForm) {
        elements.bannerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const response = await fetch('../src/banner_process.php', {
                    method: 'POST',
                    body: new FormData(elements.bannerForm)
                });
                const data = await response.json();
                if (data.success) {
                    alert('Thêm banner thành công!');
                    location.reload(); // Reload nhưng tab vẫn cố định ở "import"
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (error) {
                alert('Lỗi khi thêm banner. Vui lòng thử lại.');
            }
        });
    }

    // Xử lý nút xóa banner
    elements.deleteButtons.forEach(button => {
        button.addEventListener('click', async () => {
            if (!confirm('Bạn có chắc muốn xóa banner này?')) return;
            try {
                const response = await fetch(`../src/banner_process.php?action=delete&id=${button.dataset.id}`, {
                    method: 'GET'
                });
                const data = await response.json();
                if (data.success) {
                    alert('Xóa banner thành công!');
                    location.reload(); // Reload nhưng tab vẫn cố định ở "import"
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (error) {
                alert('Lỗi khi xóa banner. Vui lòng thử lại.');
            }
        });
    });

    // Xử lý nút chọn banner làm mặc định
    elements.setDefaultButtons.forEach(button => {
        button.addEventListener('click', async () => {
            if (!confirm('Bạn có chắc muốn đặt banner này làm mặc định?')) return;
            try {
                const response = await fetch('../src/banner_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `set_default=1&banner_id=${encodeURIComponent(button.dataset.id)}`
                });
                const data = await response.json();
                if (data.success) {
                    alert('Banner đã được đặt làm mặc định thành công!');
                    location.reload(); // Reload nhưng tab vẫn cố định ở "import"
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (error) {
                alert('Lỗi khi đặt banner mặc định. Vui lòng thử lại.');
            }
        });
    });
});