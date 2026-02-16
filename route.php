<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
} elseif (isset($_SESSION['user_id'])) {
    header("Location: public/search.php");
} else {
    header("Location: auth/login.php");
}
exit;


