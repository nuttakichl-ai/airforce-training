<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


require_once "../config/db.php";

// ตรวจ id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int)$_GET['id'];

// 🔍 ดึงชื่อไฟล์ภาพ
$stmt = $conn->prepare("SELECT image FROM training WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// ถ้าไม่พบข้อมูล
if ($result->num_rows !== 1) {
    header("Location: dashboard.php");
    exit;
}

$row = $result->fetch_assoc();

// 🗑 ลบไฟล์ภาพ (ถ้ามี)
$imagePath = "../assets/images/" . $row['image'];
if (!empty($row['image']) && file_exists($imagePath)) {
    unlink($imagePath);
}

// 🗑 ลบข้อมูลจากฐานข้อมูล
$stmt = $conn->prepare("DELETE FROM training WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// 🔙 กลับหน้า dashboard
header("Location: dashboard.php");
exit;
