<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php"); exit();
}
include 'config.php';
$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT id, subjects FROM teachers WHERE user_id=?");
mysqli_stmt_bind_param($stmt, 'i', $user_id); mysqli_stmt_execute($stmt);
$teacher = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
$teacher_id = $teacher['id'] ?? null;
$success = false; $error = ''; $saved_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_marks'])) {
    $student_id = intval($_POST['student_id'] ?? 0);
    $term = trim($_POST['term'] ?? '');
    $year = intval($_POST['year'] ?? date('Y'));
    if (!$student_id || empty($term) || !$teacher_id) {
        $error = 'Please select a student and term before submitting.';
    } else {
        $subjects = $_POST['subjects'] ?? []; $marks_in = $_POST['marks_in'] ?? [];
        $grades = $_POST['grades'] ?? []; $remarks = $_POST['remarks'] ?? [];
        foreach ($subjects as $i => $subject) {
            $subject = trim($subject); $mark = intval($marks_in[$i] ?? 0);
            $grade = trim($grades[$i] ?? ''); $remark = trim($remarks[$i] ?? '');
            if (empty($subject) || $mark < 0) continue;
            if (empty($grade)) {
                if ($mark>=80) $grade='D1'; elseif ($mark>=75) $grade='D2'; elseif ($mark>=70) $grade='C3';
                elseif ($mark>=65) $grade='C4'; elseif ($mark>=60) $grade='C5'; elseif ($mark>=55) $grade='C6';
                elseif ($mark>=50) $grade='P7'; elseif ($mark>=45) $grade='P8'; else $grade='F9';
            }
            $check = mysqli_prepare($conn, "SELECT id FROM marks WHERE student_id=? AND teacher_id=? AND subject=? AND term=? AND year=?");
            mysqli_stmt_bind_param($check, 'iissi', $student_id, $teacher_id, $subject, $term, $year);
            mysqli_stmt_execute($check); mysqli_stmt_store_result($check);
            $exists = mysqli_stmt_num_rows($check) > 0; mysqli_stmt_close($check);
            if ($exists) {
                $upd = mysqli_prepare($conn, "UPDATE marks SET marks=?, grade=?, remarks=?, status='pending' WHERE student_id=? AND teacher_id=? AND subject=? AND term=? AND year=?");
                mysqli_stmt_bind_param($upd, 'issiissi', $mark, $grade, $remark, $student_id, $teacher_id, $subject, $term, $year);
                mysqli_stmt_execute($upd); mysqli_stmt_close($upd);
            } else {
                $ins = mysqli_prepare($conn, "INSERT INTO marks (student_id, teacher_id, subject, marks, grade, remarks, term, year, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
                mysqli_stmt_bind_param($ins, 'iisiissi', $student_id, $teacher_id, $subject, $mark, $grade, $remark, $term, $year);
                mysqli_stmt_execute($ins); mysqli_stmt_close($ins);
            }
            $saved_count++;
        }
        $success = $saved_count > 0;
        if (!$success) $error = 'No marks were entered. Please fill in at least one subject.';
    }
}

$classes_result = mysqli_query($conn, "SELECT DISTINCT class FROM students ORDER BY class ASC");
$classes = [];
while ($row = mysqli_fetch_assoc($classes_result)) $classes[] = $row['class'];

$selected_class = $_POST['class'] ?? $_GET['class'] ?? '';
$selected_student = $_POST['student_id'] ?? $_GET['student_id'] ?? '';
$selected_term = $_POST['term'] ?? '';
$selected_year = $_POST['year'] ?? date('Y');

$students = [];
if (!empty($selected_class)) {
    $s = mysqli_prepare($conn, "SELECT s.id, u.full_name, s.registration_number FROM students s JOIN users u ON u.id=s.user_id WHERE s.class=? ORDER BY u.full_name ASC");
    mysqli_stmt_bind_param($s, 's', $selected_class); mysqli_stmt_execute($s);
    $sr = mysqli_stmt_get_result($s);
    while ($row = mysqli_fetch_assoc($sr)) $students[] = $row;
    mysqli_stmt_close($s);
}

$o_level_subjects = ['Mathematics','English Language','Biology','Chemistry','Physics','Geography','History','Christian Religious Education','Islamic Religious Education','Computer Studies','Literature','Kiswahili','Agriculture','Fine Art'];
$a_level_subjects = ['Mathematics','Physics','Biology','Chemistry','Economics','Geography','History','Divinity','Literature in English','General Paper','ICT','Entrepreneurship','Sub Mathematics'];
$is_alevel = in_array($selected_class, ['S5','S6']);
$subject_list = $is_alevel ? $a_level_subjects : $o_level_subjects;

$existing_marks = [];
if (!empty($selected_student) && !empty($selected_term) && $teacher_id) {
    $em = mysqli_prepare($conn, "SELECT subject, marks, grade, remarks FROM marks WHERE student_id=? AND teacher_id=? AND term=? AND year=?");
    mysqli_stmt_bind_param($em, 'iisi', $selected_student, $teacher_id, $selected_term, $selected_year);
    mysqli_stmt_execute($em); $emr = mysqli_stmt_get_result($em);
    while ($row = mysqli_fetch_assoc($emr)) $existing_marks[$row['subject']] = $row;
    mysqli_stmt_close($em);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | ENTER MARKS</title>
<link rel="stylesheet" href="style.css">
<style>
.marks-table input[type="number"]{width:90px;padding:7px 10px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;text-align:center;background:#FAFBFD}
.marks-table input[type="number"]:focus{outline:none;border-color:var(--primary-light);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.marks-table input[type="text"]{padding:7px 10px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;background:#FAFBFD;width:100%}
.marks-table input[type="text"]:focus{outline:none;border-color:var(--primary-light)}
.grade-cell{font-weight:700;text-align:center;font-family:'Poppins',sans-serif;font-size:14px}
</style>
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Enter Student Marks</h2></div>
  <div class="portal-nav">
    <a href="teacherDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>
<div class="page-wrapper">

<?php if ($success): ?>
<div class="alert alert-success">✓ <?= $saved_count ?> subject mark(s) saved successfully and submitted for approval.</div>
<?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<!-- SELECTOR -->
<div class="card">
  <h2 class="card-title">Select Class, Student & Term</h2>
  <div style="height:14px"></div>
  <form method="POST">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:14px;align-items:flex-end;flex-wrap:wrap">
      <div class="form-group" style="margin:0">
        <label class="form-label">Class</label>
        <select name="class" class="form-control" onchange="this.form.submit()">
          <option value="">-- Class --</option>
          <?php foreach ($classes as $c): ?>
          <option value="<?=$c?>" <?=$selected_class===$c?'selected':''?>><?=$c?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">Student</label>
        <select name="student_id" class="form-control" <?=empty($students)?'disabled':''?>>
          <option value="">-- Student --</option>
          <?php foreach ($students as $s): ?>
          <option value="<?=$s['id']?>" <?=$selected_student==$s['id']?'selected':''?>><?= htmlspecialchars($s['full_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">Term</label>
        <select name="term" class="form-control">
          <option value="">-- Term --</option>
          <?php foreach (['Term 1','Term 2','Term 3'] as $t): ?>
          <option value="<?=$t?>" <?=$selected_term===$t?'selected':''?>><?=$t?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin:0">
        <label class="form-label">Year</label>
        <select name="year" class="form-control">
          <?php for ($y=date('Y'); $y>=date('Y')-2; $y--): ?>
          <option value="<?=$y?>" <?=$selected_year==$y?'selected':''?>><?=$y?></option>
          <?php endfor; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-top:22px">Load</button>
    </div>
  </form>
</div>

<!-- MARKS TABLE -->
<?php if (!empty($selected_student) && !empty($selected_term) && !empty($selected_class)): ?>
<form method="POST">
  <input type="hidden" name="class" value="<?= htmlspecialchars($selected_class) ?>">
  <input type="hidden" name="student_id" value="<?= htmlspecialchars($selected_student) ?>">
  <input type="hidden" name="term" value="<?= htmlspecialchars($selected_term) ?>">
  <input type="hidden" name="year" value="<?= htmlspecialchars($selected_year) ?>">

  <div class="card">
    <h2 class="card-title">
      Enter Marks — <?= htmlspecialchars($selected_term) ?> <?= htmlspecialchars($selected_year) ?>
      <span style="font-family:'Inter',sans-serif;font-size:13px;font-weight:400;color:var(--text-muted);margin-left:12px"><?= $is_alevel ? 'A-Level Subjects' : 'O-Level Subjects' ?></span>
    </h2>
    <div style="height:14px"></div>
    <div class="table-wrapper">
      <table class="marks-table">
        <thead>
          <tr><th>Subject</th><th style="width:120px;text-align:center">Marks (/100)</th><th style="width:80px;text-align:center">Grade</th><th>Remarks</th></tr>
        </thead>
        <tbody>
          <?php foreach ($subject_list as $i => $subject):
            $ex = $existing_marks[$subject] ?? null;
            $ex_marks = $ex['marks'] ?? '';
            $ex_grade = $ex['grade'] ?? '';
            $ex_remarks = $ex['remarks'] ?? '';
          ?>
          <tr>
            <td>
              <input type="hidden" name="subjects[]" value="<?= htmlspecialchars($subject) ?>">
              <strong><?= htmlspecialchars($subject) ?></strong>
              <?php if ($ex): ?><span class="badge badge-info" style="margin-left:8px;font-size:10px">Saved</span><?php endif; ?>
            </td>
            <td style="text-align:center">
              <input type="number" name="marks_in[]" min="0" max="100" value="<?= htmlspecialchars($ex_marks) ?>"
                     onchange="autoGrade(this)" placeholder="0–100">
            </td>
            <td class="grade-cell">
              <input type="text" name="grades[]" value="<?= htmlspecialchars($ex_grade) ?>" maxlength="2" placeholder="Auto" style="width:60px;text-align:center" readonly>
            </td>
            <td>
              <input type="text" name="remarks[]" value="<?= htmlspecialchars($ex_remarks) ?>" placeholder="Optional remark">
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="btn-row" style="margin-top:20px">
      <button type="submit" name="submit_marks" class="btn btn-primary">Submit Marks for Approval</button>
      <a href="teacherDB.php" class="btn btn-gray">Cancel</a>
    </div>
    <p style="font-size:12px;color:var(--text-muted);margin-top:10px">Grades are calculated automatically. Submitted marks go to admin for approval before students can see them.</p>
  </div>
</form>
<?php endif; ?>

</div>
<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>

<script>
function autoGrade(input) {
    const marks = parseInt(input.value);
    const row = input.closest('tr');
    const gradeInput = row.querySelector('input[name="grades[]"]');
    if (isNaN(marks) || marks < 0) { gradeInput.value = ''; return; }
    let grade = '';
    if (marks >= 80) grade = 'D1';
    else if (marks >= 75) grade = 'D2';
    else if (marks >= 70) grade = 'C3';
    else if (marks >= 65) grade = 'C4';
    else if (marks >= 60) grade = 'C5';
    else if (marks >= 55) grade = 'C6';
    else if (marks >= 50) grade = 'P7';
    else if (marks >= 45) grade = 'P8';
    else grade = 'F9';
    gradeInput.value = grade;
}
</script>
</body>
</html>
