<?php

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['id'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: ../member/dangnhap.php?error=" . urlencode("Bạn cần đăng nhập để truy cập trang này."));
    exit();
}

// Kiểm tra vai trò của người dùng
$role = $_SESSION['role'];

// Kiểm tra quyền truy cập cho trang check.php
if (strpos($_SERVER['REQUEST_URI'], '../member/check.php') !== false && $role != 'user' && $role != 'admin') {
    header("Location: ../member/dangnhap.php?error=" . urlencode("Bạn không có quyền truy cập trang này."));
    exit();
}

// Kiểm tra quyền truy cập cho trang dashboard.php
if (strpos($_SERVER['REQUEST_URI'], '../admin/dashboard.php') !== false && $role = 'admin') {
    header("Location: ../member/dangnhap.php?error=" . urlencode("Bạn không có quyền truy cập trang này."));
    exit();
}

// Kiểm tra quyền truy cập cho trang dashboard.php
if (strpos($_SERVER['REQUEST_URI'], '../admin/importcode.php') !== false && $role = 'admin') {
    header("Location: ../member/dangnhap.php?error=" . urlencode("Bạn không có quyền truy cập trang này."));
    exit();
}

// Kiểm tra quyền truy cập cho trang dashboard.php
if (strpos($_SERVER['REQUEST_URI'], '../admin/showcode.php') !== false && $role = 'admin') {
    header("Location: ../member/dangnhap.php?error=" . urlencode("Bạn không có quyền truy cập trang này."));
    exit();
}

// Kiểm tra quyền truy cập cho trang dashboard.php
if (strpos($_SERVER['REQUEST_URI'], '../admin/account_management.php') !== false && $role = 'admin') {
    header("Location: ../member/dangnhap.php?error=" . urlencode("Bạn không có quyền truy cập trang này."));
    exit();
}

?>