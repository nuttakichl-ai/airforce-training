<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


require_once "../config/db.php";
$categories = require '../config/category.php';

// รับ id
$id = $_GET['id'] ?? 0;

// ดึงข้อมูลเดิม
$sql = "SELECT * FROM training WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo "ไม่พบข้อมูล";
    exit;
}

// เมื่อกดบันทึก
if (isset($_POST['update'])) {

    $title    = $_POST['title'];
    $category = $_POST['category'];
    $desc     = $_POST['description'];
    $benefit  = $_POST['benefit'];
    $command  = $_POST['command'];
    $youtube  = $_POST['youtube_url'];

    // ใช้รูปเดิม
    $img = $row['image'];

    // ถ้าอัปโหลดรูปใหม่
    if (!empty($_FILES['image']['name'])) {

        if ($img && file_exists("../assets/images/".$img)) {
            unlink("../assets/images/".$img);
        }

        $img = $_FILES['image']['name'];
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/images/".$img
        );
    }

    $sql = "UPDATE training SET
        title=?,
        description=?,
        benefit=?,
        command=?,
        category=?,
        image=?,
        youtube_url=?
        WHERE id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $title,
        $desc,
        $benefit,
        $command,
        $category,
        $img,
        $youtube,
        $id
    );
    $stmt->execute();

    $_SESSION['success'] = '✏️ แก้ไขท่าฝึกเรียบร้อยแล้ว';
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>แก้ไขท่าฝึก</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ================= FONT ================= */
body{
    font-family: 'Mitr', 'Google Sans', sans-serif;
    background: #f7f9fc;
<style>
body{
    min-height:100vh;
    background:linear-gradient(135deg,#020617,#0f172a);
}

/* White Card */
.white-card{
    background:#ffffff;
    border-radius:22px;
    padding:40px;
    box-shadow:0 30px 80px rgba(0,0,0,.35);
    animation: slideUp .6s ease;
}

@keyframes slideUp{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1;transform:none}
}

.page-title{
    font-weight:700;
    color:#0f172a;
}

label{
    font-weight:600;
    color:#334155;
    margin-bottom:6px;
}

.form-control,
.form-select{
    border-radius:14px;
    padding:12px 14px;
}

.form-control:focus,
.form-select:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,.15);
}

/* Image preview */
.preview img{
    max-height:180px;
    border-radius:16px;
    box-shadow:0 12px 30px rgba(0,0,0,.25);
    animation: zoom .4s ease;
}

@keyframes zoom{
    from{transform:scale(.85);opacity:0}
    to{transform:scale(1);opacity:1}
}

/* Buttons */
.btn-save{
    background:linear-gradient(135deg,#facc15,#f59e0b);
    border:none;
    color:#000;
    padding:12px 28px;
    font-size:18px;
    border-radius:16px;
    transition:.3s;
}
.btn-save:hover{
    transform:translateY(-3px) scale(1.05);
    box-shadow:0 12px 30px rgba(250,204,21,.45);
}

.btn-back{
    border-radius:16px;
    padding:12px 24px;
}
</style>
</head>

<body>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-7 col-md-9">

<div class="white-card">

<h3 class="page-title text-center mb-4">
✏️ แก้ไขท่าฝึก
</h3>

<form method="post" enctype="multipart/form-data">

<label>ชื่อท่าฝึก</label>
<input type="text" name="title" class="form-control mb-3"
       value="<?= htmlspecialchars($row['title']) ?>" required>

<label>หมวดท่าฝึก</label>
<select name="category" class="form-select mb-3" required>
<?php foreach($categories as $key=>$label): ?>
<option value="<?= $key ?>" <?= $row['category']==$key?'selected':'' ?>>
    <?= $label ?>
</option>
<?php endforeach; ?>
</select>

<label>คำอธิบายขั้นตอน</label>
<textarea name="description" class="form-control mb-3" rows="3" required><?= htmlspecialchars($row['description']) ?></textarea>

<label>ประโยชน์ของท่า</label>
<textarea name="benefit" class="form-control mb-3" rows="2"><?= htmlspecialchars($row['benefit']) ?></textarea>

<label>คำบอกคำสั่ง</label>
<input type="text" name="command" class="form-control mb-3"
       value="<?= htmlspecialchars($row['command']) ?>">

<label>ภาพปัจจุบัน</label>
<div class="preview mb-3">
<?php if($row['image']): ?>
<img src="../assets/images/<?= $row['image'] ?>">
<?php else: ?>
<span class="text-muted">ไม่มีภาพ</span>
<?php endif; ?>
</div>

<label>เปลี่ยนภาพ (ไม่เลือก = ใช้รูปเดิม)</label>
<input type="file" name="image" class="form-control mb-3"
       accept="image/*" onchange="previewNew(this)">

<label>ลิงก์ YouTube</label>
<input type="text" name="youtube_url" class="form-control mb-4"
       value="<?= htmlspecialchars($row['youtube_url']) ?>">

<div class="d-flex justify-content-between">
    <a href="dashboard.php" class="btn btn-outline-secondary btn-back">
        ← ย้อนกลับ
    </a>

    <button type="submit" name="update" class="btn-save">
        💾 บันทึกการแก้ไข
    </button>
</div>

</form>

</div>
</div>
</div>
</div>

<script>
function previewNew(input){
    const box = document.querySelector('.preview');
    const file = input.files[0];
    if(!file) return;

    box.innerHTML = `<img src="${URL.createObjectURL(file)}">`;
}
</script>

</body>
</html>
