<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']); $action = $_GET['action'];
    if (in_array($action, ['approve','reject'])) {
        $status = $action === 'approve' ? 'Approved' : 'Rejected';
        $stmt = mysqli_prepare($conn, "UPDATE applications SET status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'si', $status, $id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
        if ($action === 'approve') {
            $app = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM applications WHERE id=$id"));
            if ($app) { include_once 'mailer.php'; notify_application_approved($app['guardian_email'],$app['guardian_name'],$app['first_name'].' '.$app['last_name'],$app['class_applied']); }
        }
    }
    header("Location: viewapplications.php?filter=" . ($_GET['filter'] ?? 'Pending')); exit();
}
$filter = $_GET['filter'] ?? 'Pending';
if (!in_array($filter, ['Pending','Approved','Rejected','All'])) $filter = 'Pending';
$where = $filter !== 'All' ? "WHERE status='$filter'" : '';
$apps = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM applications $where ORDER BY submitted_at DESC"), MYSQLI_ASSOC);
$counts = ['Pending'=>0,'Approved'=>0,'Rejected'=>0];
$cr = mysqli_query($conn, "SELECT status, COUNT(*) as c FROM applications GROUP BY status");
while ($row = mysqli_fetch_assoc($cr)) $counts[$row['status']] = $row['c'];
$counts['All'] = array_sum($counts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | VIEW APPLICATIONS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Admission Applications</h2></div>
  <div class="portal-nav">
    <a href="staffDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>
<div class="page-wrapper">

  <div class="stats-grid">
    <div class="stat-card blue"><div class="stat-num"><?=$counts['All']?></div><div class="stat-label">Total Applications</div></div>
    <div class="stat-card gold"><div class="stat-num"><?=$counts['Pending']?></div><div class="stat-label">Pending Review</div></div>
    <div class="stat-card green"><div class="stat-num"><?=$counts['Approved']?></div><div class="stat-label">Approved</div></div>
    <div class="stat-card red"><div class="stat-num"><?=$counts['Rejected']?></div><div class="stat-label">Rejected</div></div>
  </div>

  <div class="filter-tabs">
    <?php foreach (['Pending','Approved','Rejected','All'] as $f): ?>
    <a href="?filter=<?=$f?>" class="filter-tab <?=$filter===$f?'active':''?>"><?=$f?> (<?=$counts[$f]?>)</a>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <h2 class="card-title">Applications — <?=$filter?> (<?=count($apps)?>)</h2>
    <div style="height:14px"></div>
    <?php if (empty($apps)): ?>
      <div class="notification-empty">No <?= strtolower($filter) ?> applications found.</div>
    <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead><tr><th>#</th><th>Student Name</th><th>Class Applied</th><th>Type</th><th>Guardian</th><th>Phone</th><th>Email</th><th>Submitted</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          <?php foreach ($apps as $i => $a): ?>
          <tr>
            <td><?=$i+1?></td>
            <td><strong><?= htmlspecialchars($a['first_name'].' '.$a['last_name']) ?></strong></td>
            <td><?= htmlspecialchars($a['class_applied']) ?></td>
            <td><?= htmlspecialchars($a['student_type']) ?></td>
            <td><?= htmlspecialchars($a['guardian_name']) ?></td>
            <td><?= htmlspecialchars($a['guardian_phone']) ?></td>
            <td><?= htmlspecialchars($a['guardian_email']) ?></td>
            <td><?= date('d M Y', strtotime($a['submitted_at'])) ?></td>
            <td><span class="badge <?= $a['status']==='Approved'?'badge-success':($a['status']==='Pending'?'badge-warning':'badge-danger') ?>"><?=$a['status']?></span></td>
            <td>
              <?php if ($a['status'] === 'Pending'): ?>
              <div style="display:flex;gap:6px">
                <a href="?action=approve&id=<?=$a['id']?>&filter=<?=$filter?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this application?')">Approve</a>
                <a href="?action=reject&id=<?=$a['id']?>&filter=<?=$filter?>" class="btn btn-danger btn-sm" onclick="return confirm('Reject this application?')">Reject</a>
              </div>
              <?php else: ?><span style="font-size:13px;color:var(--text-muted)">Done</span><?php endif; ?>
            </td>
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
