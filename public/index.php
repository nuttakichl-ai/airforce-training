<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>ค้นหาท่าฝึก</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&family=Mitr:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ===== GLOBAL FONT ===== */
body{
    font-family: 'Mitr', 'Google Sans', sans-serif;
    background:#f8fafc;
}

/* ===== HEADER ===== */
h3{
    font-weight:600;
}

/* ===== SEARCH ===== */
.search-box{
    border-radius:14px;
    padding:14px;
    background:#ffffff;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

/* ===== CARD ===== */
.card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 18px 45px rgba(0,0,0,.12);
    transition:.35s ease;
}
.card:hover{
    transform:translateY(-8px);
    box-shadow:0 30px 70px rgba(0,0,0,.18);
}
.card-img-top{
    height:220px;
    object-fit:cover;
}
.card-body h5{
    font-weight:500;
}

/* ===== BUTTON ===== */
.btn-primary{
    font-family:'Google Sans',sans-serif;
    border-radius:14px;
    padding:8px 16px;
    box-shadow:0 8px 20px rgba(37,99,235,.45);
}
</style>
</head>

<body>

<div class="container mt-4">

    <h3 class="mb-3">🔍 ค้นหาท่าฝึก</h3>

    <form method="get" class="search-box mb-4">
        <input class="form-control"
               name="q"
               placeholder="พิมพ์ชื่อท่าฝึก..."
               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </form>

    <div class="row mt-3">
    <?php
    $q = $_GET['q'] ?? '';
    $sql = "SELECT * FROM training WHERE title LIKE '%$q%'";
    $res = $conn->query($sql);
    while($row=$res->fetch_assoc()):
    ?>

        <div class="col-md-4">
            <div class="card mb-4">
                <img src="../assets/images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top">
                <div class="card-body">
                    <h5><?= htmlspecialchars($row['title']) ?></h5>
                    <a href="detail.php?id=<?= $row['id'] ?>"
                       class="btn btn-sm btn-primary">
                       ดูรายละเอียด
                    </a>
                </div>
            </div>
        </div>

    <?php endwhile; ?>
    </div>

</div>

</body>
</html>
