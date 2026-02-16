<?php
include '../config/db.php';

function youtubeEmbed($url){
    preg_match('/(youtu\.be\/|v=)([a-zA-Z0-9_-]+)/', $url, $matches);
    return $matches[2] ?? '';
}

$id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM training WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if(!$row){
    echo "ไม่พบข้อมูล";
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($row['title']) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Mitr',sans-serif;
    background:linear-gradient(135deg,#020617,#0f172a);
    color:#e5e7eb;
    line-height:1.55;
}

/* MAIN CARD */
.main-card{
    background:rgba(255,255,255,.06);
    backdrop-filter:blur(14px);
    border-radius:22px;
    padding:24px;
    box-shadow:0 25px 60px rgba(0,0,0,.45);
}

/* TITLE */
.page-title{
    font-size:32px;
    font-weight:700;
    text-align:center;
    margin-bottom:18px;
}

/* IMAGE LEFT */
.preview-img{
    width:100%;
    height:100%;
    object-fit:contain;
    background:#020617;
    border-radius:18px;
    padding:10px;
    box-shadow:0 14px 35px rgba(0,0,0,.45);
}

/* VIDEO RIGHT */
.video-box{
    background:#ffffff;
    border-radius:18px;
    padding:10px;
    box-shadow:0 14px 35px rgba(0,0,0,.35);
}
.video-embed{
    position:relative;
    padding-bottom:56.25%;
    height:0;
    overflow:hidden;
    border-radius:14px;
}
.video-embed iframe{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    border:0;
}

/* TEXT SECTION */
.text-section{
    max-width:820px;
    margin:24px auto 0;
    text-align:center;
}

.section-title{
    font-size:18px;
    font-weight:600;
    color:#93c5fd;
    margin-bottom:6px;
}

.section-text{
    font-size:16px;
    line-height:1.6;
    margin-bottom:16px;
}

/* COMMAND */
.command-box{
    font-size:22px;
    font-weight:600;
    color:#38bdf8;
    margin-top:6px;
}

/* BACK */
.btn-back{
    background:#1e293b;
    color:#e5e7eb;
    border:none;
    border-radius:50px;
    padding:10px 26px;
}
.btn-back:hover{
    background:#334155;
}
</style>
</head>

<body>

<div class="container my-4">

<div class="main-card">

<!-- TITLE -->
<div class="page-title">
    <?= htmlspecialchars($row['title']) ?>
</div>

<!-- IMAGE + VIDEO -->
<div class="row g-3 align-items-stretch">
    <div class="col-md-6">
        <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>"
             class="preview-img"
             alt="ภาพท่าฝึก">
    </div>

    <?php if(!empty($row['youtube_url'])): ?>
    <div class="col-md-6">
        <div class="video-box h-100">
            <div class="video-embed">
                <iframe
                    src="https://www.youtube.com/embed/<?= youtubeEmbed($row['youtube_url']) ?>"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- TEXT CENTER -->
<div class="text-section">

    <div class="section-title">คำอธิบายท่า</div>
    <div class="section-text" style="white-space:pre-line;">
        <?= nl2br(htmlspecialchars($row['description'])) ?>
    </div>

    <div class="section-title">ประโยชน์ของท่า</div>
    <div class="section-text" style="white-space:pre-line;">
        <?= nl2br(htmlspecialchars($row['benefit'])) ?>
    </div>

    <div class="section-title">คำบอกคำสั่ง</div>
    <div class="command-box">
        <?= htmlspecialchars($row['command']) ?>
    </div>

</div>

</div>

<!-- BACK -->
<div class="text-center mt-3">
    <a href="search.php" class="btn btn-back">
        ← กลับหน้าค้นหา
    </a>
</div>

</div>

</body>
</html>
