// Hàm để hiển thị modal
function showModal(message) {
    document.getElementById('errorMessage').innerText = message;
    document.getElementById('errorModal').style.display = "block";
}

// Hàm để đóng modal
function closeModal() {
    document.getElementById('errorModal').style.display = "none";
}

// Hàm để chuyển hướng về trang đăng nhập
function redirectToLogin() {
    window.location.href = "../member/dangnhap.php";
}

// Kiểm tra xem có thông báo lỗi từ URL không
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');
if (error) {
    showModal(decodeURIComponent(error));
}