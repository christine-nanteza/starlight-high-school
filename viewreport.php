<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php"); exit();
}
include 'config.php';
$teacher_user_id = $_SESSION['user_id'];

// Get all classes
$classes = mysqli_fetch_all(mysqli_query($conn, "SELECT DISTINCT class FROM students ORDER BY class ASC"), MYSQLI_ASSOC);

// Get students for selected class
$selected_class   = $_GET['class']      ?? '';
$selected_student = intval($_GET['student_id'] ?? 0);
$selected_term    = $_GET['term']        ?? '';
$selected_year    = $_GET['year']        ?? date('Y');

$students = [];
if (!empty($selected_class)) {
    $s = mysqli_prepare($conn, "SELECT s.id, u.full_name, s.registration_number FROM students s JOIN users u ON u.id=s.user_id WHERE s.class=? ORDER BY u.full_name ASC");
    mysqli_stmt_bind_param($s, 's', $selected_class);
    mysqli_stmt_execute($s);
    $sr = mysqli_stmt_get_result($s);
    while ($row = mysqli_fetch_assoc($sr)) $students[] = $row;
    mysqli_stmt_close($s);
}

// Get student details
$student = null;
if ($selected_student) {
    $stmt = mysqli_prepare($conn, "SELECT s.id, s.registration_number, s.class, u.full_name FROM students s JOIN users u ON u.id=s.user_id WHERE s.id=?");
    mysqli_stmt_bind_param($stmt, 'i', $selected_student);
    mysqli_stmt_execute($stmt);
    $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

// Get all available approved terms for selected student
$terms = [];
if ($student) {
    $t = mysqli_prepare($conn, "SELECT DISTINCT term, year FROM marks WHERE student_id=? AND status='approved' ORDER BY year DESC, term ASC");
    mysqli_stmt_bind_param($t, 'i', $student['id']);
    mysqli_stmt_execute($t);
    $tr = mysqli_stmt_get_result($t);
    while ($row = mysqli_fetch_assoc($tr)) $terms[] = $row;
    mysqli_stmt_close($t);
    if (empty($selected_term) && !empty($terms)) {
        $selected_term = $terms[0]['term'];
        $selected_year = $terms[0]['year'];
    }
}

// Get ALL marks for selected term at once
$marks = []; $total = 0;
if ($student && !empty($selected_term)) {
    $m = mysqli_prepare($conn, "SELECT subject, marks, grade, remarks FROM marks WHERE student_id=? AND term=? AND year=? AND status='approved' ORDER BY subject ASC");
    mysqli_stmt_bind_param($m, 'iss', $student['id'], $selected_term, $selected_year);
    mysqli_stmt_execute($m);
    $mr = mysqli_stmt_get_result($m);
    while ($row = mysqli_fetch_assoc($mr)) { $marks[] = $row; $total += $row['marks']; }
    mysqli_stmt_close($m);
}

$count   = count($marks);
$average = $count > 0 ? round($total / $count, 1) : 0;
$overall = '';
if ($average>=80) $overall='D1'; elseif ($average>=75) $overall='D2';
elseif ($average>=70) $overall='C3'; elseif ($average>=65) $overall='C4';
elseif ($average>=60) $overall='C5'; elseif ($average>=55) $overall='C6';
elseif ($average>=50) $overall='P7'; elseif ($average>=45) $overall='P8';
elseif ($average>0)   $overall='F9';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | VIEW REPORT</title>
<link rel="stylesheet" href="style.css">
<style>
.grade-d1,.grade-d2{color:var(--success);font-weight:700}
.grade-c3,.grade-c4,.grade-c5,.grade-c6{color:var(--primary-light);font-weight:700}
.grade-p7,.grade-p8{color:var(--warning);font-weight:700}
.grade-f9{color:var(--danger);font-weight:700}
.summary-band{background:linear-gradient(135deg,var(--primary-dark),var(--primary));border-radius:var(--radius-lg);padding:24px 32px;color:var(--white);display:flex;align-items:center;justify-content:space-around;flex-wrap:wrap;gap:20px;margin-bottom:24px}
.summary-item{text-align:center}
.summary-item .s-num{font-family:'Poppins',sans-serif;font-size:32px;font-weight:800;line-height:1}
.summary-item .s-label{font-size:12px;color:rgba(255,255,255,0.75);margin-top:4px;text-transform:uppercase;letter-spacing:0.5px}
.grade-badge{font-family:'Poppins',sans-serif;font-size:42px;font-weight:800;color:var(--accent-light)}
</style>
</head>
<body>

<div class="portal-header">
  <div class="portal-header-left">
    <h1>Starlight High School</h1>
    <h2>View Student Report Cards</h2>
  </div>
  <div class="portal-nav">
    <a href="teacherDB.php">Dashboard</a>
    <?php if ($student && !empty($marks)): ?>
    <a href="printreport.php?student_id=<?= $student['id'] ?>&term=<?= urlencode($selected_term) ?>&year=<?= $selected_year ?>" target="_blank">🖨️ Print</a>
    <?php endif; ?>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<div class="page-wrapper-md">

  <!-- SELECTOR CARD -->
  <div class="card">
    <h2 class="card-title">Select Student &amp; Term</h2>
    <div style="height:14px"></div>
    <form method="GET" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:14px;align-items:flex-end;flex-wrap:wrap">

      <div class="form-group" style="margin:0">
        <label class="form-label">Class</label>
        <select name="class" class="form-control" onchange="this.form.submit()">
          <option value="">-- Select Class --</option>
          <?php foreach ($classes as $c): ?>
          <option value="<?= $c['class'] ?>" <?= $selected_class===$c['class']?'selected':'' ?>><?= $c['class'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin:0">
        <label class="form-label">Student</label>
        <select name="student_id" class="form-control" <?= empty($students)?'disabled':'' ?>>
          <option value="">-- Select Student --</option>
          <?php foreach ($students as $s): ?>
          <option value="<?= $s['id'] ?>" <?= $selected_student==$s['id']?'selected':'' ?>><?= htmlspecialchars($s['full_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin:0">
        <label class="form-label">Term</label>
        <select name="term" class="form-control">
          <option value="">-- Term --</option>
          <?php foreach (['Term 1','Term 2','Term 3'] as $t): ?>
          <option value="<?= $t ?>" <?= $selected_term===$t?'selected':'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin:0">
        <label class="form-label">Year</label>
        <select name="year" class="form-control">
          <?php for ($y=date('Y'); $y>=date('Y')-2; $y--): ?>
          <option value="<?= $y ?>" <?= $selected_year==$y?'selected':'' ?>><?= $y ?></option>
          <?php endfor; ?>
        </select>
      </div>

      <button type="submit" class="btn btn-primary" style="margin-top:22px">View</button>
    </form>
  </div>

  <?php if ($student && !empty($marks)): ?>

  <!-- STUDENT INFO -->
  <div class="card">
    <h2 class="card-title">Student Information</h2>
    <div style="height:14px"></div>
    <div class="info-grid">
      <div class="info-item"><div class="info-label">Full Name</div><div class="info-value"><?= htmlspecialchars($student['full_name']) ?></div></div>
      <div class="info-item"><div class="info-label">Registration No.</div><div class="info-value"><?= htmlspecialchars($student['registration_number']) ?></div></div>
      <div class="info-item"><div class="info-label">Class</div><div class="info-value"><?= htmlspecialchars($student['class']) ?></div></div>
      <div class="info-item"><div class="info-label">Term / Year</div><div class="info-value"><?= htmlspecialchars($selected_term) ?> — <?= $selected_year ?></div></div>
    </div>
  </div>

  <!-- SUMMARY BAND -->
  <div class="summary-band">
    <div class="summary-item">
      <div class="s-num"><?= $count ?></div>
      <div class="s-label">Subjects</div>
    </div>
    <div class="summary-item">
      <div class="s-num"><?= $total ?></div>
      <div class="s-label">Total Marks</div>
    </div>
    <div class="summary-item">
      <div class="s-num"><?= $average ?>%</div>
      <div class="s-label">Average</div>
    </div>
    <div class="summary-item">
      <div class="grade-badge"><?= $overall ?></div>
      <div class="s-label">Overall Grade</div>
    </div>
  </div>

  <!-- FULL MARKS TABLE -->
  <div class="card">
    <h2 class="card-title">Results — <?= htmlspecialchars($selected_term) ?> <?= $selected_year ?></h2>
    <div style="height:14px"></div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Subject</th>
            <th style="text-align:center">Marks (/100)</th>
            <th style="text-align:center">Grade</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($marks as $j => $m): ?>
          <tr>
            <td><?= $j+1 ?></td>
            <td><strong><?= htmlspecialchars($m['subject']) ?></strong></td>
            <td style="text-align:center;font-size:16px;font-weight:700"><?= $m['marks'] ?></td>
            <td style="text-align:center"><span class="grade-<?= strtolower($m['grade']) ?>" style="font-size:15px"><?= htmlspecialchars($m['grade']) ?></span></td>
            <td style="color:var(--text-muted)"><?= htmlspecialchars($m['remarks'] ?: '—') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- GRADING KEY -->
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border)">
      <p style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px">Grading Scale</p>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        <?php foreach (['D1'=>'80-100','D2'=>'75-79','C3'=>'70-74','C4'=>'65-69','C5'=>'60-64','C6'=>'55-59','P7'=>'50-54','P8'=>'45-49','F9'=>'0-44'] as $g=>$r): ?>
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:5px 10px;text-align:center;min-width:60px">
          <div style="font-family:'Poppins',sans-serif;font-size:12px;font-weight:700;color:var(--primary)"><?= $g ?></div>
          <div style="font-size:10px;color:var(--text-muted)"><?= $r ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="btn-row" style="margin-top:20px">
      <a href="printreport.php?student_id=<?= $student['id'] ?>&term=<?= urlencode($selected_term) ?>&year=<?= $selected_year ?>" target="_blank" class="btn btn-primary">🖨️ Print Report Card</a>
    </div>
  </div>

  <?php elseif ($student && empty($marks) && !empty($selected_term)): ?>
  <div class="card">
    <div class="notification-empty">No approved results found for <?= htmlspecialchars($student['full_name']) ?> in <?= htmlspecialchars($selected_term) ?> <?= $selected_year ?>.</div>
  </div>

  <?php elseif ($student && empty($terms)): ?>
  <div class="card">
    <div class="notification-empty">No approved results available yet for <?= htmlspecialchars($student['full_name']) ?>.</div>
  </div>
  <?php endif; ?>

</div>

<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
