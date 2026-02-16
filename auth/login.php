<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ===== ADMIN =====
    $sqlAdmin = "SELECT id, username, password FROM admins WHERE username = ? LIMIT 1";
    $stmtAdmin = $conn->prepare($sqlAdmin);
    $stmtAdmin->bind_param("s", $username);
    $stmtAdmin->execute();
    $resAdmin = $stmtAdmin->get_result();

    if ($resAdmin->num_rows === 1) {
        $admin = $resAdmin->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: ../admin/dashboard.php");
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        // ===== USER =====
        $sqlUser = "SELECT id, username, password FROM users WHERE username = ? LIMIT 1";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bind_param("s", $username);
        $stmtUser->execute();
        $resUser = $stmtUser->get_result();

        if ($resUser->num_rows === 1) {
            $user = $resUser->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ../public/search.php");
                exit;
            } else {
                $error = "รหัสผ่านไม่ถูกต้อง";
            }
        } else {
            $error = "ไม่พบชื่อผู้ใช้งาน";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เข้าสู่ระบบ | Air Force Training</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
html,body{
    height:100%;
    overflow:hidden;
}
body{
    font-family:system-ui,-apple-system,"Segoe UI",sans-serif;
    background:linear-gradient(135deg,#ffffff,#eaf3ff);
    color:#0f172a;
}

/* ================= BACKGROUND PLANES ================= */
.sky{
    position:fixed;
    inset:0;
    overflow:hidden;
    z-index:0;
}
.plane{
    position:absolute;
    font-size:80px;
    opacity:.08;
    color:#94a3b8;
    animation:fly linear infinite;
}
.plane.one{
    top:20%;
    left:-15%;
    animation-duration:90s;
}
.plane.two{
    top:55%;
    left:-20%;
    font-size:60px;
    animation-duration:120s;
}
.plane.three{
    top:75%;
    left:-10%;
    font-size:90px;
    animation-duration:150s;
}
@keyframes fly{
    from{transform:translateX(0)}
    to{transform:translateX(140vw)}
}

/* ================= CENTER ================= */
.center-wrapper{
    min-height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
    z-index:2;
}

/* ================= LOGIN BOX ================= */
.login-box{
    background:rgba(255,255,255,.85);
    backdrop-filter:blur(14px);
    max-width:380px;
    width:100%;
    padding:34px;
    border-radius:26px;
    box-shadow:0 30px 70px rgba(0,0,0,.18);
    animation:enter .8s ease;
}
@keyframes enter{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1}
}

/* ================= TEXT ================= */
.logo{
    font-size:70px;
    animation:pulse 3s ease-in-out infinite;
}
@keyframes pulse{
    0%,100%{transform:scale(1)}
    50%{transform:scale(1.05)}
}
.subtitle{
    color:#64748b;
    font-size:14px;
    margin-bottom:22px;
}

/* ================= INPUT ================= */
.form-control{
    border-radius:14px;
    padding:12px 14px;
}
.form-control:focus{
    box-shadow:0 0 0 3px rgba(59,130,246,.25);
    border-color:#3b82f6;
}

/* ================= BUTTON ================= */
.btn-main{
    background:linear-gradient(135deg,#60a5fa,#2563eb);
    border:none;
    border-radius:16px;
    font-size:18px;
    padding:12px;
    color:#fff;
    transition:.3s;
}
.btn-main:hover{
    transform:translateY(-3px);
    box-shadow:0 14px 35px rgba(37,99,235,.35);
}

/* ================= ERROR ================= */
.alert{
    background:#fee2e2;
    border:none;
    color:#991b1b;
}
</style>
</head>

<body>

<!-- BACKGROUND -->
<div class="sky">
    <div class="plane one">✈️</div>
    <div class="plane two">✈️</div>
    <div class="plane three">✈️</div>
</div>

<div class="center-wrapper">
<div class="login-box text-center">

    <div class="logo">✈️</div>
    <h5 class="fw-bold mt-2">Air Force Wing 46</h5>
    <div class="subtitle">ระบบท่าฝึกทางทหาร</div>

    <?php if($error): ?>
        <div class="alert text-start">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3 text-start">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control form-control-lg" required>
        </div>

        <div class="mb-3 text-start">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-lg" required>
        </div>

        <button type="submit" class="btn btn-main w-100">
            🚀 เข้าสู่ระบบ
        </button>
    </form>

</div>
</div>

</body>
</html>
