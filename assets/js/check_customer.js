document.addEventListener('DOMContentLoaded', () => {
    const activateForm = document.getElementById('activateForm');
    const resultBox = document.getElementById('resultBox');
    const importFile = document.getElementById('importFile');
    const importButton = document.getElementById('importButton');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressDiv = document.getElementById('progress');
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');

    if (!window.csrfToken) {
        console.error('CSRF token không được định nghĩa.');
        if (resultBox) {
            resultBox.style.display = 'block';
            resultBox.classList.add('error');
            resultBox.innerHTML = '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Lỗi: CSRF token không được cung cấp.</p>';
        }
        return;
    }

    // Xử lý form kích hoạt
    if (activateForm && resultBox) {
        activateForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const code = document.getElementById('code').value.trim();
            if (!code) {
                showResult({ success: false, message: '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Vui lòng nhập mã CODE.</p>' });
                return;
            }

            resultBox.style.display = 'block';
            resultBox.classList.remove('success', 'error');
            resultBox.classList.add('processing');
            resultBox.innerHTML = '<p>Đang xử lý, vui lòng đợi...</p>';

            const formData = {
                customer_name: document.getElementById('customer_name').value.trim(),
                phone_number: document.getElementById('phone_number').value.trim(),
                email: document.getElementById('email').value.trim(),
                purchase_location: document.getElementById('purchase_location').value,
                city_province: document.getElementById('city_province').value,
                code: code,
                csrf_token: window.csrfToken
            };

            try {
                const response = await fetch('./src/activate_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    throw new Error(`Lỗi HTTP: ${response.status}`);
                }

                const data = await response.json();
                showResult(data);

                if (data.success && data.newly_activated) {
                    activateForm.reset();
                }

            } catch (error) {
                showResult({ success: false, message: '<strong>THÔNG BÁO:</strong><p style="color:red;font-weight:600;">Lỗi kết nối: ' + error.message + '. Vui lòng thử lại.</p>' });
            }
        });
    }

    // Xử lý tabs
    if (tabButtons.length && tabPanes.length) {
        const currentTab = localStorage.getItem('currentTab') || 'activate';

        tabPanes.forEach(pane => pane.classList.remove('active'));
        const activePane = document.getElementById(currentTab);
        if (activePane) activePane.classList.add('active');

        tabButtons.forEach(button => {
            if (button.getAttribute('data-tab') === currentTab) {
                button.classList.add('active');
            }
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(tabId).classList.add('active');
                localStorage.setItem('currentTab', tabId);
            });
        });
    }

    // Xử lý import
    if (importFile && importButton && progressBar && progressText && progressDiv) {
        importFile.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file && file.name.endsWith('.csv')) {
                importButton.disabled = false;
            } else {
                alert('Vui lòng chọn file CSV.');
                importButton.disabled = true;
                e.target.value = '';
            }
        });

        importButton.addEventListener('click', () => {
            const file = importFile.files[0];
            if (!file) {
                alert('Vui lòng chọn file CSV trước.');
                return;
            }
        
            progressDiv.style.display = 'block';
            progressBar.value = 0;
            progressText.textContent = '0%';
        
            let offset = 0;
            let totalImported = 0;
        
            function importNextChunk() {
                const formData = new FormData();
                formData.append('csv_file', file);
                formData.append('offset', offset);
                formData.append('limit', 10000); // Khớp với PHP
        
                fetch('./src/import_process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Lỗi server: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response from server:', data); // Debug
        
                    if (!data.success) {
                        throw new Error(data.message);
                    }
        
                    totalImported += data.imported;
                    const progress = Math.min(100, (totalImported / data.total_data_lines) * 100);
                    progressBar.value = progress;
                    progressText.textContent = `${Math.round(progress)}%`;
        
                    if (data.next_offset !== null) {
                        offset = data.next_offset;
                        console.log('Next chunk at offset:', offset);
                        setTimeout(importNextChunk, 1000);
                    } else {
                        if (totalImported !== data.total_data_lines) {
                            throw new Error(`Chỉ import được ${totalImported} dòng, thiếu ${data.total_data_lines - totalImported} dòng so với ${data.total_data_lines} dòng dữ liệu.`);
                        }
        
                        alert('Import hoàn tất!');
                        showResult({
                            success: true,
                            message: `Hoàn thành import: ${totalImported} dòng dữ liệu.`
                        });
        
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1500);
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    progressBar.value = 100;
                    progressText.textContent = '100%';
                    showResult({
                        success: false,
                        message: 'Lỗi khi import: ' + error.message
                    });
                });
            }
        
            importNextChunk();
        });
    }

    // Hàm hiển thị kết quả
    function showResult(data) {
        if (!resultBox) return;
        resultBox.style.display = 'block';
        resultBox.classList.remove('processing', 'success', 'error');
        resultBox.classList.add(data.success ? 'success' : 'error');
        let resultHTML = data.message;
        if (data.success && data.newly_activated) {
            resultHTML += `<p><strong>Mã voucher của bạn: ${data.voucher_code}</strong></p>`;
        }
        resultBox.innerHTML = resultHTML;
        resultBox.scrollIntoView({ behavior: 'smooth' });
    }

    // Xử lý input số điện thoại
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0 && value[0] !== '0') value = '0' + value;
            if (value.length > 10) value = value.substring(0, 10);
            e.target.value = value;
        });
    }
});