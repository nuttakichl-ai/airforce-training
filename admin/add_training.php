<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}


require '../config/db.php';
$categories = require '../config/category.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title']);
    $category    = $_POST['category'];
    $description = trim($_POST['description']);
    $benefit     = trim($_POST['benefit']);
    $command     = trim($_POST['command']);
    $youtube     = trim($_POST['youtube_url']);

    /* ================= IMAGE UPLOAD ================= */
    if (!empty($_FILES['image']['name'])) {

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allow = ['jpg','jpeg','png','webp'];

        if (!in_array(strtolower($ext), $allow)) {
            die("ไฟล์รูปไม่ถูกต้อง");
        }

        $imageName = time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = "../assets/images/" . $imageName;

        move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
    }

    /* ================= INSERT ================= */
    $sql = "INSERT INTO training
            (title, description, benefit, command, image, youtube_url, category)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssss",
        $title,
        $description,
        $benefit,
        $command,
        $imageName,
        $youtube,
        $category
    );
    $stmt->execute();

    $_SESSION['success'] = "เพิ่มท่าฝึกเรียบร้อยแล้ว";
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>เพิ่มท่าฝึก</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Mitr',sans-serif;
    background:linear-gradient(135deg,#020617,#0f172a);
    min-height:100vh;
}

.white-card{
    background:#fff;
    border-radius:22px;
    padding:40px;
    box-shadow:0 30px 80px rgba(0,0,0,.35);
    animation:fadeUp .6s ease;
}
@keyframes fadeUp{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1}
}

.page-title{
    font-weight:700;
    color:#0f172a;
}

label{
    font-weight:600;
    margin-bottom:6px;
}

.form-control,.form-select{
    border-radius:14px;
    padding:12px;
}

.preview{
    display:none;
    margin-top:15px;
}
.preview img{
    max-height:180px;
    border-radius:14px;
    box-shadow:0 12px 30px rgba(0,0,0,.25);
}

.btn-save{
    background:linear-gradient(135deg,#22c55e,#16a34a);
    color:#fff;
    border:none;
    padding:12px 30px;
    border-radius:16px;
    font-size:18px;
}
.btn-save:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 35px rgba(34,197,94,.45);
}
</style>
</head>

<body>

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-lg-7">

<div class="white-card">

<h3 class="page-title text-center mb-4">
    ➕ เพิ่มท่าฝึกใหม่
</h3>

<form method="post" enctype="multipart/form-data">

<label>ชื่อท่าฝึก</label>
<input type="text" name="title" class="form-control mb-3" required>

<label>หมวดท่าฝึก</label>
<select name="category" class="form-select mb-3" required>
    <option value="">-- เลือกหมวด --</option>
    <?php foreach($categories as $key=>$label): ?>
        <option value="<?= $key ?>"><?= $label ?></option>
    <?php endforeach; ?>
</select>

<label>คำอธิบายขั้นตอน</label>
<textarea name="description" rows="3" class="form-control mb-3" required></textarea>

<label>ประโยชน์ของท่า</label>
<textarea name="benefit" rows="2" class="form-control mb-3"></textarea>

<label>คำบอกคำสั่ง</label>
<input type="text" name="command" class="form-control mb-3">

<label>ภาพประกอบ</label>
<input type="file"
       name="image"
       class="form-control mb-2"
       accept="image/*"
       onchange="previewImage(this)"
       required>

<div class="preview" id="previewBox">
    <img id="previewImg">
</div>

<label class="mt-3">ลิงก์ YouTube (ถ้ามี)</label>
<input type="text"
       name="youtube_url"
       class="form-control mb-4"
       placeholder="https://www.youtube.com/watch?v=xxxx">

<div class="d-flex justify-content-between">
    <a href="dashboard.php" class="btn btn-outline-secondary px-4">
        ← ย้อนกลับ
    </a>
    <button type="submit" class="btn-save">
        💾 บันทึกข้อมูล
    </button>
</div>

</form>

</div>
</div>
</div>
</div>

<script>
function previewImage(input){
    const box = document.getElementById('previewBox');
    const img = document.getElementById('previewImg');
    const file = input.files[0];
    if(!file) return;
    img.src = URL.createObjectURL(file);
    box.style.display = 'block';
}
</script>

</body>
</html>
