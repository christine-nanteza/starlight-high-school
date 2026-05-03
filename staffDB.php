<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';
$full_name = $_SESSION['full_name'];

$total_students = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM students"))['c'];
$total_teachers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM teachers"))['c'];
$pending_apps   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM applications WHERE status='Pending'"))['c'];
$pending_marks  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM marks WHERE status='pending'"))['c'];

$students = mysqli_fetch_all(mysqli_query($conn,
    "SELECT s.id, u.full_name, u.email, s.registration_number, s.class, s.student_type
     FROM students s JOIN users u ON u.id=s.user_id ORDER BY s.class, u.full_name ASC"), MYSQLI_ASSOC);

$teachers = mysqli_fetch_all(mysqli_query($conn,
    "SELECT t.id, u.full_name, u.email, t.staff_id, t.subjects, t.phone
     FROM teachers t JOIN users u ON u.id=t.user_id ORDER BY u.full_name ASC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | ADMIN DASHBOARD</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Admin / Staff Portal</h2></div>
  <div class="portal-nav">
    <a href="home.html">🏠 Home</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<div class="page-wrapper">

  <div class="welcome-banner">
    <h2>Welcome, <span class="accent-text"><?= htmlspecialchars($full_name) ?></span> 👋</h2>
    <p>Manage students, teachers, applications, marks and finances from your admin dashboard.</p>
  </div>

  <?php if (isset($_GET['msg'])): ?>
  <div class="alert <?= str_contains($_GET['msg'],'deleted')?'alert-success':'alert-info' ?>">
    <?php
    $msgs = [
      'student_deleted' => '✓ Student has been deleted successfully.',
      'teacher_deleted' => '✓ Teacher has been deleted successfully.',
    ];
    echo $msgs[$_GET['msg']] ?? 'Action completed.';
    ?>
  </div>
  <?php endif; ?>

  <!-- STATS -->
  <div class="stats-grid">
    <div class="stat-card blue"><div class="stat-num"><?= $total_students ?></div><div class="stat-label">Total Students</div></div>
    <div class="stat-card green"><div class="stat-num"><?= $total_teachers ?></div><div class="stat-label">Total Teachers</div></div>
    <div class="stat-card gold"><div class="stat-num"><?= $pending_apps ?></div><div class="stat-label">Pending Applications</div></div>
    <div class="stat-card red"><div class="stat-num"><?= $pending_marks ?></div><div class="stat-label">Marks Awaiting Approval</div></div>
  </div>

  <!-- STUDENTS TABLE -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
      <h2 class="card-title" style="margin:0;padding:0">Students (<?= count($students) ?>)</h2>
      <a href="addstud.php" class="btn btn-primary">+ Add New Student</a>
    </div>

    <?php if (empty($students)): ?>
    <div class="notification-empty">No students added yet. Click "Add New Student" to get started.</div>
    <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr><th>#</th><th>Full Name</th><th>Reg No.</th><th>Class</th><th>Type</th><th>Email</th><th style="text-align:center">Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($students as $i => $s): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><strong><?= htmlspecialchars($s['full_name']) ?></strong></td>
            <td style="font-family:'Poppins',sans-serif;font-size:13px;color:var(--primary)"><?= htmlspecialchars($s['registration_number']) ?></td>
            <td><span class="badge badge-primary"><?= htmlspecialchars($s['class']) ?></span></td>
            <td><span class="badge <?= $s['student_type']==='Boarding'?'badge-info':'badge-gray' ?>"><?= htmlspecialchars($s['student_type'] ?: 'Day') ?></span></td>
            <td style="font-size:13px;color:var(--text-muted)"><?= htmlspecialchars($s['email']) ?></td>
            <td>
              <div style="display:flex;gap:6px;justify-content:center">
                <a href="editstud.php?id=<?= $s['id'] ?>" class="btn btn-gray btn-sm">✏️ Edit</a>
                <a href="deletestud.php?id=<?= $s['id'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('⚠️ Delete <?= htmlspecialchars(addslashes($s['full_name'])) ?>?\n\nThis will permanently delete their account, marks and fee records. This cannot be undone.')">
                   🗑️ Delete
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- TEACHERS TABLE -->
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
      <h2 class="card-title" style="margin:0;padding:0">Teachers (<?= count($teachers) ?>)</h2>
      <a href="addtr.php" class="btn btn-primary">+ Add New Teacher</a>
    </div>

    <?php if (empty($teachers)): ?>
    <div class="notification-empty">No teachers added yet. Click "Add New Teacher" to get started.</div>
    <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr><th>#</th><th>Full Name</th><th>Staff ID</th><th>Subjects</th><th>Email</th><th>Phone</th><th style="text-align:center">Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($teachers as $i => $t): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><strong><?= htmlspecialchars($t['full_name']) ?></strong></td>
            <td style="font-family:'Poppins',sans-serif;font-size:13px;color:var(--primary)"><?= htmlspecialchars($t['staff_id']) ?></td>
            <td style="font-size:13px"><?= htmlspecialchars($t['subjects'] ?: '—') ?></td>
            <td style="font-size:13px;color:var(--text-muted)"><?= htmlspecialchars($t['email']) ?></td>
            <td style="font-size:13px"><?= htmlspecialchars($t['phone'] ?: '—') ?></td>
            <td>
              <div style="display:flex;gap:6px;justify-content:center">
                <a href="edittr.php?id=<?= $t['id'] ?>" class="btn btn-gray btn-sm">✏️ Edit</a>
                <a href="deletetr.php?id=<?= $t['id'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('⚠️ Delete <?= htmlspecialchars(addslashes($t['full_name'])) ?>?\n\nThis will permanently delete their account and all marks they submitted. This cannot be undone.')">
                   🗑️ Delete
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- ADMIN TOOLS -->
  <div class="card">
    <h2 class="card-title">Admin Tools</h2>
    <div style="height:14px"></div>
    <div class="menu-grid">
      <a href="viewapplications.php" class="menu-card">
        <span class="menu-icon">📋</span>
        <span class="menu-label">View Applications</span>
      </a>
      <a href="admin_approve.php" class="menu-card">
        <span class="menu-icon">✅</span>
        <span class="menu-label">Approve Marks</span>
      </a>
      <a href="managefees.php" class="menu-card">
        <span class="menu-icon">💰</span>
        <span class="menu-label">Manage Fees</span>
      </a>
      <a href="financialoverview.php" class="menu-card">
        <span class="menu-icon">📈</span>
        <span class="menu-label">Financial Overview</span>
      </a>
    </div>
  </div>

</div>

<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
