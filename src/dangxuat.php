<?php
session_start();
session_destroy();
header("Location: ../member/dangnhap.php");
exit();
?>