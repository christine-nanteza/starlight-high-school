<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';
$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fee'])) {
    $student_id = intval($_POST['student_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $paid = floatval($_POST['paid'] ?? 0);
    $term = trim($_POST['term'] ?? '');
    $year = intval($_POST['year'] ?? date('Y'));
    $date_paid = trim($_POST['date_paid'] ?? '');
    $status = $paid >= $amount ? 'Paid' : ($paid > 0 ? 'Partial' : 'Pending');
    if (!$student_id || empty($description) || $amount <= 0 || empty($term)) {
        $error = 'Please fill in all required fields.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO fees (student_id, description, amount, paid, term, year, status, date_paid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $dpv = !empty($date_paid) ? $date_paid : null;
        mysqli_stmt_bind_param($stmt, 'isddsiis', $student_id, $description, $amount, $paid, $term, $year, $status, $dpv);
        mysqli_stmt_execute($stmt) ? $success = 'Fee record added.' : $error = 'Failed to add fee record.';
        mysqli_stmt_close($stmt);
    }
}
if (isset($_GET['delete'])) {
    $d = mysqli_prepare($conn, "DELETE FROM fees WHERE id = ?"); $did = intval($_GET['delete']);
    mysqli_stmt_bind_param($d, 'i', $did); mysqli_stmt_execute($d); mysqli_stmt_close($d);
    header("Location: managefees.php?msg=deleted"); exit();
}
if (isset($_GET['markpaid'])) {
    $mid = intval($_GET['markpaid']); $today = date('Y-m-d');
    $mp = mysqli_prepare($conn, "UPDATE fees SET paid=amount, status='Paid', date_paid=? WHERE id=?");
    mysqli_stmt_bind_param($mp, 'si', $today, $mid); mysqli_stmt_execute($mp); mysqli_stmt_close($mp);
    header("Location: managefees.php?msg=paid"); exit();
}

$students = mysqli_fetch_all(mysqli_query($conn, "SELECT s.id, u.full_name, s.registration_number, s.class FROM students s JOIN users u ON u.id=s.user_id ORDER BY s.class, u.full_name ASC"), MYSQLI_ASSOC);
$filter_student = intval($_GET['student_id'] ?? 0);
$fee_sql = "SELECT f.*, u.full_name, s.registration_number, s.class FROM fees f JOIN students s ON s.id=f.student_id JOIN users u ON u.id=s.user_id" . ($filter_student ? " WHERE f.student_id=$filter_student" : "") . " ORDER BY f.year DESC, f.term ASC, u.full_name ASC";
$fees = mysqli_fetch_all(mysqli_query($conn, $fee_sql), MYSQLI_ASSOC);
$fee_presets = ['Tuition Fee'=>800000,'Examination Fee'=>50000,'Uniform & Books'=>200000,'Boarding & Meals'=>500000,'Activity Fee'=>30000];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | MANAGE FEES</title>
<link rel="stylesheet" href="style.css">
<style>
.fees-layout{display:grid;grid-template-columns:380px 1fr;gap:22px;align-items:start}
@media(max-width:900px){.fees-layout{grid-template-columns:1fr}}
.preset-grid{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px}
.preset-btn{padding:5px 12px;background:var(--bg);border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;color:var(--primary);transition:all .2s}
.preset-btn:hover{background:var(--primary);color:var(--white);border-color:var(--primary)}
.action-btns{display:flex;gap:6px}
</style>
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Fee Management</h2></div>
  <div class="portal-nav">
    <a href="staffDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>
<div class="page-wrapper">
<div class="fees-layout">

  <!-- ADD FORM -->
  <div>
  <div class="card" style="margin-bottom:0">
    <h2 class="card-title">Add Fee Record</h2>
    <div style="height:14px"></div>
    <?php if (!empty($success)): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (isset($_GET['msg'])): ?><div class="alert alert-success"><?= $_GET['msg']==='deleted'?'Fee record deleted.':'Fee marked as paid.' ?></div><?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Student</label>
        <select name="student_id" class="form-control" required>
          <option value="">-- Select Student --</option>
          <?php foreach ($students as $s): ?>
          <option value="<?=$s['id']?>" <?=$filter_student==$s['id']?'selected':''?>><?= htmlspecialchars($s['full_name']) ?> — <?=$s['class']?> (<?=$s['registration_number']?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Quick-Fill Presets</label>
        <div class="preset-grid">
          <?php foreach ($fee_presets as $name => $amt): ?>
          <button type="button" class="preset-btn" onclick="document.querySelector('[name=description]').value='<?=$name?>';document.querySelector('[name=amount]').value='<?=$amt?>'">
            <?= $name ?>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <input type="text" name="description" class="form-control" placeholder="e.g. Tuition Fee Term 1" required>
      </div>
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">Amount (UGX)</label>
          <input type="number" name="amount" class="form-control" placeholder="800000" min="0" required>
        </div>
        <div class="form-group">
          <label class="form-label">Paid (UGX)</label>
          <input type="number" name="paid" class="form-control" placeholder="0" min="0" value="0">
        </div>
        <div class="form-group">
          <label class="form-label">Term</label>
          <select name="term" class="form-control" required>
            <option value="">-- Select --</option>
            <option>Term 1</option><option>Term 2</option><option>Term 3</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Year</label>
          <select name="year" class="form-control">
            <?php for ($y=date('Y'); $y>=date('Y')-2; $y--): ?><option value="<?=$y?>"><?=$y?></option><?php endfor; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Date Paid <span class="optional">(if already paid)</span></label>
        <input type="date" name="date_paid" class="form-control">
      </div>
      <button type="submit" name="add_fee" class="btn btn-primary btn-full">Add Fee Record</button>
    </form>
  </div>
  </div>

  <!-- FEE RECORDS TABLE -->
  <div>
  <div class="card" style="margin-bottom:0">
    <h2 class="card-title">Fee Records</h2>
    <div style="height:14px"></div>
    <div style="display:flex;gap:12px;align-items:flex-end;margin-bottom:18px;flex-wrap:wrap">
      <div>
        <label class="form-label">Filter by Student</label>
        <select class="form-control" style="min-width:220px" onchange="window.location='managefees.php?student_id='+this.value">
          <option value="0">All Students</option>
          <?php foreach ($students as $s): ?>
          <option value="<?=$s['id']?>" <?=$filter_student==$s['id']?'selected':''?>><?= htmlspecialchars($s['full_name']) ?> (<?=$s['class']?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php if ($filter_student): ?><a href="managefees.php" class="btn btn-gray btn-sm" style="margin-top:22px">Clear</a><?php endif; ?>
    </div>
    <?php if (empty($fees)): ?>
      <div class="notification-empty">No fee records found.</div>
    <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead><tr><th>Student</th><th>Class</th><th>Description</th><th>Term/Yr</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($fees as $f): $bal = $f['amount'] - $f['paid']; ?>
          <tr>
            <td><strong><?= htmlspecialchars($f['full_name']) ?></strong><br><span style="font-size:11px;color:var(--text-muted)"><?=$f['registration_number']?></span></td>
            <td><?=$f['class']?></td>
            <td><?= htmlspecialchars($f['description']) ?></td>
            <td><?=$f['term']?> <?=$f['year']?></td>
            <td><?= number_format($f['amount']) ?></td>
            <td><?= number_format($f['paid']) ?></td>
            <td><?= number_format($bal) ?></td>
            <td><span class="badge <?= $f['status']==='Paid'?'badge-success':($f['status']==='Partial'?'badge-warning':'badge-danger') ?>"><?=$f['status']?></span></td>
            <td>
              <div class="action-btns">
                <?php if ($f['status'] !== 'Paid'): ?>
                <a href="managefees.php?markpaid=<?=$f['id']?>" class="btn btn-success btn-sm" onclick="return confirm('Mark as fully paid?')">Paid</a>
                <?php endif; ?>
                <a href="managefees.php?delete=<?=$f['id']?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">Del</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
  </div>

</div>
</div>
<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
