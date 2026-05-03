<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php"); exit();
}
include 'config.php';
$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT id, class FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
$fees = []; $total_paid = 0; $total_pending = 0;
if ($student) {
    $f = mysqli_prepare($conn, "SELECT description, amount, paid, term, year, status, date_paid FROM fees WHERE student_id = ? ORDER BY year DESC, term ASC");
    mysqli_stmt_bind_param($f, 'i', $student['id']);
    mysqli_stmt_execute($f);
    $fr = mysqli_stmt_get_result($f);
    while ($row = mysqli_fetch_assoc($fr)) {
        $fees[] = $row;
        if ($row['status'] === 'Paid') $total_paid += $row['amount'];
        else $total_pending += ($row['amount'] - $row['paid']);
    }
    mysqli_stmt_close($f);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | FINANCIAL STATEMENT</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Financial Statement</h2></div>
  <div class="portal-nav">
    <a href="studentDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>
<div class="page-wrapper-md">

  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-num" style="font-size:19px">UGX <?= number_format($total_paid + $total_pending) ?></div>
      <div class="stat-label">Total Billed</div>
    </div>
    <div class="stat-card green">
      <div class="stat-num" style="font-size:19px;color:var(--success)">UGX <?= number_format($total_paid) ?></div>
      <div class="stat-label">Total Paid</div>
    </div>
    <div class="stat-card gold">
      <div class="stat-num" style="font-size:19px;color:var(--warning)">UGX <?= number_format($total_pending) ?></div>
      <div class="stat-label">Balance Due</div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Fee Records</h2>
    <div style="height:14px"></div>
    <?php if (empty($fees)): ?>
      <div class="notification-empty">No fee records found. Contact the school administration.</div>
    <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead><tr><th>Description</th><th>Term / Year</th><th>Amount (UGX)</th><th>Paid (UGX)</th><th>Balance (UGX)</th><th>Status</th><th>Date Paid</th></tr></thead>
        <tbody>
          <?php foreach ($fees as $f): $balance = $f['amount'] - $f['paid']; ?>
          <tr>
            <td><?= htmlspecialchars($f['description']) ?></td>
            <td><?= htmlspecialchars($f['term']) ?> <?= $f['year'] ?></td>
            <td><?= number_format($f['amount']) ?></td>
            <td><?= number_format($f['paid']) ?></td>
            <td><?= number_format($balance) ?></td>
            <td><span class="badge <?= $f['status']==='Paid'?'badge-success':($f['status']==='Partial'?'badge-warning':'badge-danger') ?>"><?= $f['status'] ?></span></td>
            <td><?= !empty($f['date_paid']) ? date('d M Y', strtotime($f['date_paid'])) : '—' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2 class="card-title">How to Pay School Fees</h2>
    <div style="height:14px"></div>
    <div class="alert alert-info">To make a payment, use one of the methods below and bring your receipt to the school bursar. Your balance will be updated after confirmation.</div>
    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label">Select Payment Method</label>
        <select class="form-control">
          <option>-- Select Method --</option>
          <option>Mobile Money — MTN: 0700 567 891</option>
          <option>Mobile Money — Airtel: 0750 567 891</option>
          <option>Bank Transfer — Stanbic Bank A/C: 9030012345678</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Amount to Pay (UGX)</label>
        <input type="number" class="form-control" placeholder="Enter amount" value="<?= number_format($total_pending,0,'','') ?>" min="0">
      </div>
    </div>
    <button type="button" class="btn btn-primary">Confirm Payment Method</button>
  </div>

</div>
<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
