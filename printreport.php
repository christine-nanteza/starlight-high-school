<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$role    = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Teachers and admins can print any student's report
// Students can only print their own
if ($role === 'student') {
    $stmt = mysqli_prepare($conn,
        "SELECT s.id, s.registration_number, s.class, s.student_type,
                u.full_name
         FROM students s JOIN users u ON u.id = s.user_id
         WHERE u.id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
} else {
    $student_id = intval($_GET['student_id'] ?? 0);
    if (!$student_id) { echo "No student selected."; exit(); }
    $stmt = mysqli_prepare($conn,
        "SELECT s.id, s.registration_number, s.class, s.student_type,
                u.full_name
         FROM students s JOIN users u ON u.id = s.user_id
         WHERE s.id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $student_id);
    mysqli_stmt_execute($stmt);
    $student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

if (!$student) { echo "Student not found."; exit(); }

$selected_term = $_GET['term'] ?? '';
$selected_year = $_GET['year'] ?? date('Y');

// If no term passed and student role, use latest term with marks
if (empty($selected_term)) {
    $lt = mysqli_prepare($conn,
        "SELECT term, year FROM marks WHERE student_id = ? AND status='approved'
         ORDER BY year DESC, term DESC LIMIT 1");
    mysqli_stmt_bind_param($lt, 'i', $student['id']);
    mysqli_stmt_execute($lt);
    $ltrow = mysqli_fetch_assoc(mysqli_stmt_get_result($lt));
    mysqli_stmt_close($lt);
    if ($ltrow) { $selected_term = $ltrow['term']; $selected_year = $ltrow['year']; }
}

// Get marks
$marks = [];
$total = 0;
if (!empty($selected_term)) {
    $m = mysqli_prepare($conn,
        "SELECT subject, marks, grade, remarks FROM marks
         WHERE student_id=? AND term=? AND year=? AND status='approved'
         ORDER BY subject ASC");
    mysqli_stmt_bind_param($m, 'iss', $student['id'], $selected_term, $selected_year);
    mysqli_stmt_execute($m);
    $mr = mysqli_stmt_get_result($m);
    while ($row = mysqli_fetch_assoc($mr)) { $marks[] = $row; $total += $row['marks']; }
    mysqli_stmt_close($m);
}
$count   = count($marks);
$average = $count > 0 ? round($total / $count, 1) : 0;
$overall = '';
if      ($average >= 80) $overall = 'D1';
elseif  ($average >= 75) $overall = 'D2';
elseif  ($average >= 70) $overall = 'C3';
elseif  ($average >= 65) $overall = 'C4';
elseif  ($average >= 60) $overall = 'C5';
elseif  ($average >= 55) $overall = 'C6';
elseif  ($average >= 50) $overall = 'P7';
elseif  ($average >= 45) $overall = 'P8';
elseif  ($average >  0)  $overall = 'F9';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card — <?= htmlspecialchars($student['full_name']) ?></title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background: #f0f4f8; color: #1a1a1a; }

        /* SCREEN ONLY */
        .screen-bar { background: #1f3c88; color: white; padding: 14px 30px; display: flex; align-items: center; justify-content: space-between; }
        .screen-bar span { font-size: 15px; font-weight: bold; }
        .screen-bar button { padding: 9px 24px; background: white; color: #1f3c88; border: none; border-radius: 6px; font-weight: bold; font-size: 14px; cursor: pointer; }
        .screen-bar button:hover { background: #dbeafe; }
        .screen-wrapper { padding: 30px 20px; display: flex; justify-content: center; }

        /* REPORT CARD */
        .report { background: white; width: 210mm; min-height: 297mm; padding: 16mm; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }

        /* HEADER */
        .report-header { text-align: center; border-bottom: 3px solid #1f3c88; padding-bottom: 16px; margin-bottom: 20px; }
        .school-name { font-size: 22px; font-weight: bold; color: #1f3c88; letter-spacing: 1px; }
        .school-sub  { font-size: 12px; color: #555; margin-top: 3px; }
        .report-title { font-size: 17px; font-weight: bold; color: #1f3c88; margin-top: 10px; border: 2px solid #1f3c88; display: inline-block; padding: 4px 20px; border-radius: 4px; }
        .term-label  { font-size: 13px; color: #555; margin-top: 6px; }

        /* STUDENT INFO BOX */
        .student-info { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 22px; }
        .info-cell { border: 1px solid #ddd; padding: 8px 12px; border-radius: 6px; }
        .info-cell .lbl { font-size: 10px; font-weight: bold; color: #888; text-transform: uppercase; }
        .info-cell .val { font-size: 14px; font-weight: bold; color: #1f3c88; margin-top: 2px; }

        /* MARKS TABLE */
        table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        thead th { background: #1f3c88; color: white; padding: 10px 12px; font-size: 13px; text-align: left; }
        thead th:not(:first-child) { text-align: center; }
        tbody td { padding: 9px 12px; border-bottom: 1px solid #e8ecf0; font-size: 13px; }
        tbody td:not(:first-child) { text-align: center; }
        tbody tr:nth-child(even) { background: #f7f9fc; }
        .grade-D1, .grade-D2 { color: #166534; font-weight: bold; }
        .grade-C3, .grade-C4, .grade-C5, .grade-C6 { color: #1d4ed8; font-weight: bold; }
        .grade-P7, .grade-P8 { color: #92400e; font-weight: bold; }
        .grade-F9 { color: #991b1b; font-weight: bold; }

        /* SUMMARY */
        .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }
        .sum-box { border: 1px solid #ddd; border-radius: 8px; padding: 12px; text-align: center; }
        .sum-box .sl { font-size: 10px; font-weight: bold; color: #888; text-transform: uppercase; }
        .sum-box .sv { font-size: 20px; font-weight: bold; color: #1f3c88; margin-top: 4px; }

        /* GRADING SCALE */
        .grade-scale { margin-bottom: 22px; }
        .grade-scale h4 { font-size: 12px; font-weight: bold; color: #555; margin-bottom: 8px; text-transform: uppercase; }
        .grade-scale-grid { display: grid; grid-template-columns: repeat(9, 1fr); gap: 6px; }
        .gs-item { border: 1px solid #ddd; border-radius: 4px; padding: 5px; text-align: center; }
        .gs-item .grade { font-size: 12px; font-weight: bold; color: #1f3c88; }
        .gs-item .range { font-size: 10px; color: #888; }

        /* SIGNATURE */
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; margin-top: 30px; }
        .sig-box { border-top: 1px solid #333; padding-top: 6px; }
        .sig-box .sig-label { font-size: 11px; color: #555; }

        /* FOOTER */
        .report-footer { text-align: center; font-size: 10px; color: #888; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 20px; }

        /* PRINT */
        @media print {
            .screen-bar { display: none !important; }
            .screen-wrapper { padding: 0; background: none; }
            .report { box-shadow: none; margin: 0; padding: 12mm; }
            body { background: white; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>

<!-- SCREEN TOP BAR (hidden on print) -->
<div class="screen-bar">
    <span>Report Card Preview — <?= htmlspecialchars($student['full_name']) ?></span>
    <button onclick="window.print()">🖨️ Print Report Card</button>
</div>

<div class="screen-wrapper">
<div class="report">

    <!-- HEADER -->
    <div class="report-header">
        <div class="school-name">STARLIGHT HIGH SCHOOL</div>
        <div class="school-sub">Private Mixed Secondary School | UNEB Curriculum | Kyengera, Kampala</div>
        <div class="school-sub">Email: info@starlighthigh.ac.ug | Phone: +256 700 567 891</div>
        <div class="report-title">STUDENT REPORT CARD</div>
        <div class="term-label"><?= htmlspecialchars($selected_term) ?> — Academic Year <?= htmlspecialchars($selected_year) ?></div>
    </div>

    <!-- STUDENT INFO -->
    <div class="student-info">
        <div class="info-cell"><div class="lbl">Student Name</div><div class="val"><?= htmlspecialchars($student['full_name']) ?></div></div>
        <div class="info-cell"><div class="lbl">Registration No.</div><div class="val"><?= htmlspecialchars($student['registration_number']) ?></div></div>
        <div class="info-cell"><div class="lbl">Class</div><div class="val"><?= htmlspecialchars($student['class']) ?></div></div>
    </div>

    <!-- MARKS TABLE -->
    <?php if (empty($marks)): ?>
        <p style="text-align:center;color:#94a3b8;padding:30px">No approved results for this term.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr><th style="width:40%">Subject</th><th>Marks (/100)</th><th>Grade</th><th>Remarks</th></tr>
        </thead>
        <tbody>
            <?php foreach ($marks as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['subject']) ?></td>
                <td><?= $m['marks'] ?></td>
                <td><span class="grade-<?= $m['grade'] ?>"><?= htmlspecialchars($m['grade']) ?></span></td>
                <td><?= htmlspecialchars($m['remarks'] ?: '—') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- SUMMARY -->
    <div class="summary">
        <div class="sum-box"><div class="sl">Total Marks</div><div class="sv"><?= $total ?></div></div>
        <div class="sum-box"><div class="sl">Average</div><div class="sv"><?= $average ?>%</div></div>
        <div class="sum-box"><div class="sl">Overall Grade</div><div class="sv"><?= $overall ?></div></div>
    </div>
    <?php endif; ?>

    <!-- GRADING SCALE -->
    <div class="grade-scale">
        <h4>Grading Scale</h4>
        <div class="grade-scale-grid">
            <?php
            $scale = ['D1'=>'80-100','D2'=>'75-79','C3'=>'70-74','C4'=>'65-69','C5'=>'60-64','C6'=>'55-59','P7'=>'50-54','P8'=>'45-49','F9'=>'0-44'];
            foreach ($scale as $g => $r): ?>
            <div class="gs-item"><div class="grade"><?=$g?></div><div class="range"><?=$r?></div></div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SIGNATURES -->
    <div class="signatures">
        <div class="sig-box"><br><br><div class="sig-label">Class Teacher's Signature</div></div>
        <div class="sig-box"><br><br><div class="sig-label">Head Teacher's Signature</div></div>
        <div class="sig-box"><br><br><div class="sig-label">Parent / Guardian's Signature</div></div>
    </div>

    <!-- REPORT FOOTER -->
    <div class="report-footer">
        This report card is issued by Starlight High School and is valid without a stamp only when collected in person.<br>
        For inquiries contact: info@starlighthigh.ac.ug | +256 700 567 891
    </div>

</div>
</div>

</body>
</html>
