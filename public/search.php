<?php
session_start();
require_once '../config/db.php';

/* ===== Guard : อนุญาตเฉพาะ USER ===== */
if (!isset($_SESSION['user_id'])) {

    if (isset($_SESSION['admin_id'])) {
        header("Location: ../admin/dashboard.php");
        exit;
    }

    header("Location: ../auth/login.php");
    exit;
}

$categories = require '../config/category.php';

/* ===== Username ===== */
$userName = $_SESSION['username'] ?? 'ผู้ใช้งาน';

/* ===== Search ===== */
$q = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM training WHERE title LIKE ?";
$params = ["%$q%"];
$types = "s";

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

/* ===== ดึงชื่อท่าฝึกทั้งหมด (ใช้ทำ typing animation) ===== */
$names = [];
$resNames = $conn->query("SELECT title FROM training");
while($r = $resNames->fetch_assoc()){
    $names[] = $r['title'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>ค้นหาท่าฝึก</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans&family=Mitr:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ===== BACKGROUND ===== */
body{
    font-family:'Mitr','Google Sans',sans-serif;
    min-height:100vh;
    background:linear-gradient(-45deg,#e8f1ff,#f0f9ff,#eef2ff,#ffffff);
    background-size:400% 400%;
    animation:bgMove 18s ease infinite;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

/* ===== GREETING BOX ===== */
.greeting-box{
    background:linear-gradient(135deg,#38bdf8,#e0f2fe,#ffffff);
    border-radius:26px;
    padding:28px 32px;
    box-shadow:0 30px 80px rgba(14,165,233,.45);
    animation:float 4s ease-in-out infinite;
    position:relative;
    overflow:hidden;
}
@keyframes float{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-6px)}
}
.greeting-box::after{
    content:"";
    position:absolute;
    inset:-50%;
    background:linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.7),
        transparent
    );
    animation:shine 6s infinite;
}
@keyframes shine{
    0%{transform:translateX(-100%)}
    100%{transform:translateX(100%)}
}

.greeting-box h2{
    font-weight:700;
    margin:0;
    color:#0f172a;
}
.user-name{
    color:#ffffff;
    font-weight:800;
    text-shadow:
        0 0 10px rgba(255,255,255,.6),
        0 0 25px rgba(186,230,253,.9);
}

/* ===== SEARCH BOX ===== */
.search-box{
    background:#fff;
    border-radius:50px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
    padding:8px;
}

/* ===== CARD ===== */
.card-hover{
    border:none;
    transition:.35s cubic-bezier(.22,1,.36,1);
}
.card-hover:hover{
    transform:translateY(-10px);
    box-shadow:0 30px 60px rgba(0,0,0,.2);
}
.img-full{
    height:220px;
    object-fit:contain;
    background:#f1f5f9;
    padding:10px;
    border-radius:20px 20px 0 0;
}
</style>
</head>

<body>

<div class="container my-5">

<!-- 🔹 GREETING -->
<div class="greeting-box mb-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <h2>
        สวัสดี 👋 <span class="user-name">คุณ <?= htmlspecialchars($userName) ?></span>
    </h2>

    <a href="../auth/logout.php" class="btn btn-outline-danger rounded-pill">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<!-- 🔹 CATEGORY -->
<ul class="nav nav-pills justify-content-center mb-4 gap-2">
<?php foreach ($categories as $key => $label): ?>
<li class="nav-item">
    <a class="nav-link px-4 rounded-pill <?= $category === $key ? 'active' : 'bg-white shadow-sm' ?>"
       href="search.php?category=<?= $key ?>">
        <?= htmlspecialchars($label) ?>
    </a>
</li>
<?php endforeach; ?>
</ul>

<!-- 🔹 SEARCH -->
<form method="get" class="mb-5">
<input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
<div class="search-box mx-auto" style="max-width:600px;">
    <div class="input-group">
        <span class="input-group-text bg-white border-0 ps-3">
            <i class="bi bi-search"></i>
        </span>
        <input type="text" id="searchInput"
               name="q"
               class="form-control border-0 shadow-none"
               placeholder=""
               value="<?= htmlspecialchars($q) ?>">
        <button class="btn btn-primary rounded-pill px-4 me-1">ค้นหา</button>
    </div>
</div>
</form>

<!-- 🔹 RESULT -->
<div class="row g-4">
<?php if ($result->num_rows == 0): ?>
<div class="text-center text-muted py-5">
    <p>ไม่พบข้อมูลท่าฝึก</p>
</div>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
<div class="col-lg-4 col-md-6">
    <div class="card card-hover h-100 rounded-4">
        <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>" class="img-full">
        <div class="card-body d-flex flex-column p-4">
            <h5 class="fw-bold mb-3"><?= htmlspecialchars($row['title']) ?></h5>
            <a href="detail.php?id=<?= $row['id'] ?>"
               class="btn btn-outline-primary rounded-pill mt-auto">
               ดูรายละเอียด
            </a>
        </div>
    </div>
</div>
<?php endwhile; ?>
</div>

</div>

<script>
/* ===== Typing Animation ===== */
const texts = <?= json_encode($names) ?>;
let i=0,j=0,del=false;
const input=document.getElementById('searchInput');

function typing(){
    if(texts.length===0) return;
    input.placeholder=texts[i].substring(0,j);
    if(!del && j++ === texts[i].length+10) del=true;
    if(del && j-- === 0){
        del=false;
        i=(i+1)%texts.length;
    }
    setTimeout(typing, del?50:100);
}
typing();
</script>

</body>
</html>
