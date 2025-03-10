// admin/js/edit_record.js
document.addEventListener('DOMContentLoaded', function() {
    // Lấy form
    const form = document.getElementById('editRecordForm');
    
    // Thêm sự kiện submit cho form
    if (form) {
        form.addEventListener('submit', function(event) {
            // Kiểm tra dữ liệu trước khi submit
            if (!validateForm()) {
                event.preventDefault();
            }
        });
    }
    
    // Hàm kiểm tra dữ liệu form
    function validateForm() {
        let isValid = true;
        
        // Kiểm tra SerialNumber
        const serialNumber = document.getElementById('SerialNumber').value.trim();
        if (!serialNumber) {
            showError('SerialNumber', 'Serial Number không được để trống');
            isValid = false;
        } else {
            clearError('SerialNumber');
        }
        
        // Kiểm tra Email (nếu có)
        const email = document.getElementById('Email').value.trim();
        if (email && !isValidEmail(email)) {
            showError('Email', 'Email không hợp lệ');
            isValid = false;
        } else {
            clearError('Email');
        }
        
    }
});