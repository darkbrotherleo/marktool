// JavaScript để mở modal và điền thông tin người dùng
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-btn');
    const modal = document.getElementById('editModal');
    const closeButton = document.querySelector('.close');
    const editForm = document.getElementById('editForm');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const email = this.getAttribute('data-email');
            // Gửi yêu cầu AJAX để lấy thông tin người dùng dựa trên email
            fetch(`../src/get_user_info.php?email=${encodeURIComponent(email)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Điền thông tin vào form
                        document.getElementById('userId').value = data.user.id;
                        document.getElementById('email').value = data.user.email;
                        document.getElementById('role').value = data.user.role;
                        document.getElementById('status').value = data.user.status;

                        // Hiển thị modal
                        modal.style.display = 'block';
                    } else {
                        alert('Không tìm thấy thông tin người dùng.');
                    }
                })
                .catch(error => {
                    console.error('Lỗi:', error);
                });
        });
    });

    // Đóng modal khi nhấn nút đóng
    closeButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Đóng modal khi nhấn ra ngoài modal
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});