<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';
$summary = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(DISTINCT student_id) AS total_students, SUM(amount) AS total_expected, SUM(paid) AS total_collected, SUM(amount-paid) AS total_outstanding FROM fees"));
$filter_term = $_GET['term'] ?? '';
$filter_class = $_GET['class'] ?? '';
$where_parts = [];
if (!empty($filter_term))  $where_parts[] = "f.term = '" . mysqli_real_escape_string($conn, $filter_term) . "'";
if (!empty($filter_class)) $where_parts[] = "s.class = '" . mysqli_real_escape_string($conn, $filter_class) . "'";
$where = count($where_parts) ? 'WHERE ' . implode(' AND ', $where_parts) : '';
$records = mysqli_fetch_all(mysqli_query($conn,
    "SELECT u.full_name, s.registration_number, s.class, SUM(f.amount) AS total_fee, SUM(f.paid) AS amount_paid, SUM(f.amount-f.paid) AS balance,
     CASE WHEN SUM(f.amount-f.paid)=0 THEN 'Paid' WHEN SUM(f.paid)>0 THEN 'Partial' ELSE 'Outstanding' END AS status
     FROM fees f JOIN students s ON s.id=f.student_id JOIN users u ON u.id=s.user_id $where
     GROUP BY f.student_id ORDER BY s.class, u.full_name ASC"), MYSQLI_ASSOC);
$class_list = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT class FROM students ORDER BY class"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | FINANCIAL OVERVIEW</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Financial Overview</h2></div>
  <div class="portal-nav">
    <a href="staffDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>
<div class="page-wrapper">

  <div class="stats-grid">
    <div class="stat-card blue"><div class="stat-num"><?= $summary['total_students']??0 ?></div><div class="stat-label">Students with Fee Records</div></div>
    <div class="stat-card navy"><div class="stat-num" style="font-size:16px">UGX <?= number_format($summary['total_expected']??0) ?></div><div class="stat-label">Total Expected</div></div>
    <div class="stat-card green"><div class="stat-num" style="font-size:16px;color:var(--success)">UGX <?= number_format($summary['total_collected']??0) ?></div><div class="stat-label">Total Collected</div></div>
    <div class="stat-card red"><div class="stat-num" style="font-size:16px;color:var(--danger)">UGX <?= number_format($summary['total_outstanding']??0) ?></div><div class="stat-label">Total Outstanding</div></div>
  </div>

  <form method="GET" style="background:var(--white);padding:16px 20px;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);margin-bottom:20px;display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap">
    <div class="form-group" style="margin:0">
      <label class="form-label">Filter by Class</label>
      <select name="class" class="form-control" style="min-width:160px">
        <option value="">All Classes</option>
        <?php foreach ($class_list as $c): ?><option value="<?=$c['class']?>" <?=$filter_class===$c['class']?'selected':''?>><?=$c['class']?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="form-group" style="margin:0">
      <label class="form-label">Filter by Term</label>
      <select name="term" class="form-control" style="min-width:140px">
        <option value="">All Terms</option>
        <?php foreach (['Term 1','Term 2','Term 3'] as $t): ?><option value="<?=$t?>" <?=$filter_term===$t?'selected':''?>><?=$t?></option><?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="financialoverview.php" class="btn btn-gray">Clear</a>
  </form>

  <div class="card">
    <h2 class="card-title">Student Fee Records (<?= count($records) ?>)</h2>
    <div style="height:14px"></div>
    <?php if (empty($records)): ?>
      <div class="notification-empty">No fee records found.</div>
    <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead><tr><th>#</th><th>Student Name</th><th>Reg No.</th><th>Class</th><th>Total Fee (UGX)</th><th>Paid (UGX)</th><th>Balance (UGX)</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($records as $i => $r): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
            <td><?= htmlspecialchars($r['registration_number']) ?></td>
            <td><?= htmlspecialchars($r['class']) ?></td>
            <td><?= number_format($r['total_fee']) ?></td>
            <td><?= number_format($r['amount_paid']) ?></td>
            <td><?= number_format($r['balance']) ?></td>
            <td><span class="badge <?= $r['status']==='Paid'?'badge-success':($r['status']==='Partial'?'badge-warning':'badge-danger') ?>"><?= $r['status'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</div>
<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
