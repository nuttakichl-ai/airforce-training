<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/db.php";

$stmt = $conn->prepare("SELECT * FROM training ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();

$total = $conn->query("SELECT COUNT(*) t FROM training")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>

<!-- Google Font (สำคัญ) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
/* ===== GLOBAL ===== */
body{
    font-family:'Mitr','Prompt',sans-serif;
    background:linear-gradient(135deg,#020617,#0f172a,#1e293b);
    color:#e5e7eb;
    min-height:100vh;
}

/* ===== SIDEBAR ===== */
.sidebar{
    position:fixed;
    width:240px;
    height:100vh;
    padding:24px;
    background:rgba(255,255,255,.05);
    backdrop-filter:blur(14px);
    border-right:1px solid rgba(255,255,255,.1);
}
.sidebar h5{
    font-weight:700;
    color:#fff;
    margin-bottom:20px;
}
.sidebar a{
    display:block;
    color:#cbd5e1;
    padding:10px 14px;
    border-radius:12px;
    text-decoration:none;
    margin-bottom:8px;
    transition:.25s;
}
.sidebar a:hover{
    background:#2563eb;
    color:#fff;
}

/* ===== CONTENT ===== */
.content{
    margin-left:260px;
    padding:30px;
}

/* ===== GLASS CARD ===== */
.glass{
    background:rgba(255,255,255,.07);
    backdrop-filter:blur(12px);
    border-radius:20px;
    padding:22px;
    box-shadow:0 15px 40px rgba(0,0,0,.45);
}

/* ===== SUMMARY ===== */
.summary{
    text-align:center;
}
.summary h1{
    font-size:56px;
    font-weight:700;
    color:#38bdf8;
}

/* ===== BUTTON ===== */
.btn-premium{
    background:linear-gradient(135deg,#2563eb,#1e40af);
    color:#fff;
    border:none;
    border-radius:14px;
    padding:10px 20px;
}

/* ===== TABLE ===== */
.table{
    color:#111;
    background:#fff;
    border-radius:16px;
    overflow:hidden;
}
.table th{
    font-size:13px;
}

/* ===== CARD VIEW ===== */
.card-view{display:none;}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
    gap:20px;
}
.training-card{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(10px);
    border-radius:18px;
    padding:14px;
}
.training-card img{
    width:100%;
    height:160px;
    object-fit:cover;
    border-radius:14px;
}

/* ===== DELETE BUTTON ===== */
.btn-delete-pro{
    background:#ef4444;
    color:#fff;
    border:none;
}

/* ===== MODAL ===== */
.modal-content{
    border-radius:18px;
}
</style>
</head>

<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
<h5>⚙️ Admin Panel</h5>

<a href="#">Dashboard</a>
<a href="add_training.php">➕ เพิ่มท่าฝึก</a>
<a href="../index.php">🏠 Home</a>
<a href="../auth/logout.php">🚪 Logout</a>

<hr class="border-secondary">
<button class="btn btn-warning btn-sm w-100" onclick="toggleDark()">Dark Mode</button>
</div>


<div class="content">

<h3 class="mb-4 fw-bold">Dashboard</h3>

<!-- SUMMARY -->
<div class="glass summary mb-4">
<h1><?= $total ?></h1>
<p>จำนวนท่าฝึกทั้งหมด</p>
</div>

<div class="mb-3 d-flex gap-2">
<button class="btn btn-light btn-sm" onclick="showTable()">ตาราง</button>
<button class="btn btn-secondary btn-sm" onclick="showCard()">การ์ด</button>
<a href="add_training.php" class="btn-premium">➕ เพิ่ม</a>
</div>


<!-- ===== TABLE ===== -->
<div id="tableView" class="glass">
<table id="trainingTable" class="table align-middle">

<thead>
<tr>
<th>ชื่อท่าฝึก</th>
<th>ภาพ</th>
<th width="160">จัดการ</th>
</tr>
</thead>

<tbody>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['title']) ?></td>

<td>
<?php if($row['image']): ?>
<img src="../assets/images/<?= $row['image'] ?>" width="70">
<?php endif; ?>
</td>

<td>
<a href="edit_training.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️</a>
<button class="btn-delete-pro btn btn-sm"
onclick="confirmDelete('<?= $row['id'] ?>','<?= htmlspecialchars($row['title']) ?>')">
ลบ
</button>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>


<!-- ===== CARD VIEW ===== -->
<div id="cardView" class="card-view mt-3">
<div class="grid">

<?php
$result->data_seek(0);
while($row=$result->fetch_assoc()):
?>

<div class="training-card">
<img src="../assets/images/<?= $row['image'] ?>">
<h6 class="mt-2 text-white"><?= htmlspecialchars($row['title']) ?></h6>

<div class="d-flex gap-2 mt-2">
<a href="edit_training.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm w-50">แก้ไข</a>
<button class="btn-delete-pro btn btn-sm w-50"
onclick="confirmDelete('<?= $row['id'] ?>','<?= htmlspecialchars($row['title']) ?>')">
ลบ
</button>
</div>
</div>

<?php endwhile; ?>

</div>
</div>

</div>


<!-- ===== DELETE MODAL ===== -->
<div class="modal fade" id="deleteModal">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-danger text-white">
<h5>ยืนยันการลบ</h5>
</div>

<div class="modal-body text-center">
ลบ <b id="deleteTitle"></b> ?
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
<a id="deleteBtn" class="btn btn-danger">ลบถาวร</a>
</div>

</div>
</div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$('#trainingTable').DataTable();

function showCard(){
    tableView.style.display='none';
    cardView.style.display='block';
}
function showTable(){
    tableView.style.display='block';
    cardView.style.display='none';
}
function toggleDark(){
    document.body.classList.toggle('bg-dark');
}
function confirmDelete(id,title){
    deleteTitle.innerText = title;
    deleteBtn.href = "delete_training.php?id="+id;
    new bootstrap.Modal(deleteModal).show();
}
</script>

</body>
</html>
