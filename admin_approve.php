<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';

// Handle approve/reject WHOLE report (student + term + year)
if (isset($_GET['action']) && isset($_GET['student_id']) && isset($_GET['term']) && isset($_GET['year'])) {
    $student_id = intval($_GET['student_id']);
    $term       = mysqli_real_escape_string($conn, $_GET['term']);
    $year       = intval($_GET['year']);
    $action     = $_GET['action'];

    if ($action === 'approve') {
        mysqli_query($conn, "UPDATE marks SET status='approved' WHERE student_id=$student_id AND term='$term' AND year=$year AND status='pending'");
    } elseif ($action === 'reject') {
        mysqli_query($conn, "UPDATE marks SET status='rejected' WHERE student_id=$student_id AND term='$term' AND year=$year AND status='pending'");
    }

    header("Location: admin_approve.php?filter=" . ($_GET['filter'] ?? 'pending'));
    exit();
}

$filter = $_GET['filter'] ?? 'pending';
if (!in_array($filter, ['pending','approved','rejected','all'])) $filter = 'pending';

// Get grouped reports: one row per student+term+year
$where = $filter !== 'all' ? "WHERE m.status = '$filter'" : '';

$sql = "SELECT 
            s.id AS student_id,
            u_s.full_name AS student_name,
            s.registration_number,
            s.class,
            m.term,
            m.year,
            m.status,
            COUNT(m.id) AS subject_count,
            SUM(m.marks) AS total_marks,
            ROUND(AVG(m.marks), 1) AS average,
            u_t.full_name AS teacher_name
        FROM marks m
        JOIN students s   ON s.id   = m.student_id
        JOIN users u_s    ON u_s.id = s.user_id
        JOIN teachers t   ON t.id   = m.teacher_id
        JOIN users u_t    ON u_t.id = t.user_id
        $where
        GROUP BY s.id, m.term, m.year, m.status
        ORDER BY m.year DESC, m.term ASC, u_s.full_name ASC";

$reports = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);

// Count badges — count distinct student+term+year groups per status
$counts = ['pending'=>0,'approved'=>0,'rejected'=>0];
$cr = mysqli_query($conn, "SELECT status, COUNT(DISTINCT CONCAT(student_id,term,year)) as c FROM marks GROUP BY status");
while ($row = mysqli_fetch_assoc($cr)) $counts[$row['status']] = $row['c'];
$counts['all'] = array_sum($counts);

// Grade calculator
function calcGrade($avg) {
    if ($avg >= 80) return 'D1';
    if ($avg >= 75) return 'D2';
    if ($avg >= 70) return 'C3';
    if ($avg >= 65) return 'C4';
    if ($avg >= 60) return 'C5';
    if ($avg >= 55) return 'C6';
    if ($avg >= 50) return 'P7';
    if ($avg >= 45) return 'P8';
    if ($avg > 0)   return 'F9';
    return '—';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | APPROVE REPORTS</title>
<link rel="stylesheet" href="style.css">
<style>
.report-card-preview {
    display: none;
    background: var(--bg);
    border-radius: var(--radius-md);
    padding: 16px;
    margin-top: 12px;
}
.report-card-preview.open { display: block; }
.subjects-mini-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.subjects-mini-table th { background: var(--primary); color: white; padding: 8px 12px; text-align: left; font-size: 12px; }
.subjects-mini-table td { padding: 8px 12px; border-bottom: 1px solid var(--border); }
.subjects-mini-table tr:last-child td { border-bottom: none; }
.subjects-mini-table tr:nth-child(even) { background: #F8FAFC; }
.report-row { background: var(--white); border-radius: var(--radius-md); margin-bottom: 14px; box-shadow: var(--shadow-sm); border-left: 4px solid var(--border); overflow: hidden; transition: all 0.2s; }
.report-row.pending  { border-color: var(--warning); }
.report-row.approved { border-color: var(--success); }
.report-row.rejected { border-color: var(--danger); }
.report-row-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; flex-wrap: wrap; gap: 12px; cursor: pointer; }
.report-row-header:hover { background: var(--bg); }
.report-info { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
.report-meta { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
.report-actions { display: flex; gap: 8px; align-items: center; }
.grade-d1,.grade-d2 { color: var(--success); font-weight: 700; }
.grade-c3,.grade-c4,.grade-c5,.grade-c6 { color: var(--primary-light); font-weight: 700; }
.grade-p7,.grade-p8 { color: var(--warning); font-weight: 700; }
.grade-f9 { color: var(--danger); font-weight: 700; }
.toggle-btn { background: var(--bg); border: 1.5px solid var(--border); border-radius: var(--radius-sm); padding: 5px 12px; font-size: 12px; font-weight: 600; cursor: pointer; color: var(--text); font-family: 'Poppins', sans-serif; }
.toggle-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }
</style>
</head>
<body>

<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Approve Student Reports</h2></div>
  <div class="portal-nav">
    <a href="staffDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<div class="page-wrapper">

  <div class="stats-grid">
    <div class="stat-card blue"><div class="stat-num"><?= $counts['all'] ?></div><div class="stat-label">Total Reports</div></div>
    <div class="stat-card gold"><div class="stat-num"><?= $counts['pending'] ?></div><div class="stat-label">Pending Approval</div></div>
    <div class="stat-card green"><div class="stat-num"><?= $counts['approved'] ?></div><div class="stat-label">Approved</div></div>
    <div class="stat-card red"><div class="stat-num"><?= $counts['rejected'] ?></div><div class="stat-label">Rejected</div></div>
  </div>

  <div class="filter-tabs">
    <?php foreach (['pending'=>'⏳ Pending','approved'=>'✅ Approved','rejected'=>'❌ Rejected','all'=>'All'] as $k=>$label): ?>
    <a href="?filter=<?= $k ?>" class="filter-tab <?= $filter===$k?'active':'' ?>"><?= $label ?> (<?= $counts[$k] ?>)</a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($reports)): ?>
    <div class="card"><div class="notification-empty">No <?= $filter ?> reports found.</div></div>
  <?php else: ?>

  <div style="margin-bottom:12px">
    <p style="font-size:14px;color:var(--text-muted)">
      Showing <strong><?= count($reports) ?></strong> report(s). Click <strong>View Subjects</strong> to preview marks before approving.
    </p>
  </div>

  <?php foreach ($reports as $i => $r):
    $grade = calcGrade($r['average']);
    $gradeClass = 'grade-' . strtolower($grade);

    // Fetch subjects for this report
    $sub_sql = "SELECT subject, marks, grade, remarks FROM marks
                WHERE student_id=? AND term=? AND year=? AND status=?
                ORDER BY subject ASC";
    $sub_stmt = mysqli_prepare($conn, $sub_sql);
    $status_filter = $filter !== 'all' ? $filter : $r['status'];
    mysqli_stmt_bind_param($sub_stmt, 'isss', $r['student_id'], $r['term'], $r['year'], $status_filter);
    mysqli_stmt_execute($sub_stmt);
    $subjects = mysqli_fetch_all(mysqli_stmt_get_result($sub_stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($sub_stmt);
  ?>

  <div class="report-row <?= $r['status'] ?>">
    <div class="report-row-header" onclick="toggleReport(<?= $i ?>)">

      <!-- LEFT: Student info -->
      <div class="report-info">
        <div>
          <div style="font-family:'Poppins',sans-serif;font-size:15px;font-weight:700;color:var(--primary)"><?= htmlspecialchars($r['student_name']) ?></div>
          <div class="report-meta"><?= htmlspecialchars($r['registration_number']) ?> &nbsp;|&nbsp; <?= htmlspecialchars($r['class']) ?></div>
        </div>
        <div style="text-align:center">
          <div style="font-family:'Poppins',sans-serif;font-size:13px;font-weight:600;color:var(--text)"><?= htmlspecialchars($r['term']) ?> <?= $r['year'] ?></div>
          <div class="report-meta">Term / Year</div>
        </div>
        <div style="text-align:center">
          <div style="font-family:'Poppins',sans-serif;font-size:18px;font-weight:700;color:var(--primary)"><?= $r['subject_count'] ?></div>
          <div class="report-meta">Subjects</div>
        </div>
        <div style="text-align:center">
          <div style="font-family:'Poppins',sans-serif;font-size:18px;font-weight:700;color:var(--primary)"><?= $r['average'] ?>%</div>
          <div class="report-meta">Average</div>
        </div>
        <div style="text-align:center">
          <div class="<?= $gradeClass ?>" style="font-size:22px"><?= $grade ?></div>
          <div class="report-meta">Grade</div>
        </div>
        <div>
          <span class="badge <?= $r['status']==='approved'?'badge-success':($r['status']==='pending'?'badge-warning':'badge-danger') ?>"><?= ucfirst($r['status']) ?></span>
        </div>
      </div>

      <!-- RIGHT: Actions -->
      <div class="report-actions" onclick="event.stopPropagation()">
        <button class="toggle-btn" onclick="toggleReport(<?= $i ?>)">👁 View Subjects</button>
        <?php if ($r['status'] === 'pending'): ?>
        <a href="?action=approve&student_id=<?= $r['student_id'] ?>&term=<?= urlencode($r['term']) ?>&year=<?= $r['year'] ?>&filter=<?= $filter ?>"
           class="btn btn-success btn-sm"
           onclick="return confirm('Approve the full report for <?= htmlspecialchars($r['student_name']) ?> — <?= htmlspecialchars($r['term']) ?> <?= $r['year'] ?>?')">
           ✅ Approve All
        </a>
        <a href="?action=reject&student_id=<?= $r['student_id'] ?>&term=<?= urlencode($r['term']) ?>&year=<?= $r['year'] ?>&filter=<?= $filter ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Reject the full report for <?= htmlspecialchars($r['student_name']) ?> — <?= htmlspecialchars($r['term']) ?> <?= $r['year'] ?>?')">
           ❌ Reject All
        </a>
        <?php else: ?>
        <span style="font-size:13px;color:var(--text-muted);font-style:italic">No action needed</span>
        <?php endif; ?>
      </div>

    </div>

    <!-- EXPANDABLE SUBJECT TABLE -->
    <div class="report-card-preview" id="report-<?= $i ?>">
      <?php if (empty($subjects)): ?>
        <p style="color:var(--text-muted);font-size:13px">No subjects found.</p>
      <?php else: ?>
      <table class="subjects-mini-table">
        <thead><tr><th>#</th><th>Subject</th><th>Marks (/100)</th><th>Grade</th><th>Remarks</th></tr></thead>
        <tbody>
          <?php foreach ($subjects as $j => $sub): ?>
          <tr>
            <td><?= $j+1 ?></td>
            <td><strong><?= htmlspecialchars($sub['subject']) ?></strong></td>
            <td style="text-align:center;font-weight:700"><?= $sub['marks'] ?></td>
            <td style="text-align:center"><span class="grade-<?= strtolower($sub['grade']) ?>"><?= htmlspecialchars($sub['grade']) ?></span></td>
            <td><?= htmlspecialchars($sub['remarks'] ?: '—') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div style="display:flex;gap:20px;margin-top:12px;padding:12px 0;border-top:1px solid var(--border)">
        <span style="font-size:13px"><strong>Total:</strong> <?= $r['total_marks'] ?></span>
        <span style="font-size:13px"><strong>Average:</strong> <?= $r['average'] ?>%</span>
        <span style="font-size:13px"><strong>Overall Grade:</strong> <span class="<?= $gradeClass ?>"><?= $grade ?></span></span>
        <span style="font-size:13px"><strong>Submitted by:</strong> <?= htmlspecialchars($r['teacher_name']) ?></span>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php endforeach; ?>
  <?php endif; ?>

</div>

<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>

<script>
function toggleReport(i) {
    const el = document.getElementById('report-' + i);
    el.classList.toggle('open');
}
</script>
</body>
</html>
