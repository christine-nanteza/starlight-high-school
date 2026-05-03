<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: staffDB.php"); exit(); }

$success = false; $error = '';

// Fetch current teacher data
$stmt = mysqli_prepare($conn,
    "SELECT u.id as user_id, u.full_name, u.email, t.staff_id,
            t.subjects, t.phone, t.gender, t.employment_date
     FROM teachers t JOIN users u ON u.id=t.user_id WHERE t.id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data) { header("Location: staffDB.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = trim($_POST['first_name']      ?? '');
    $last_name   = trim($_POST['last_name']       ?? '');
    $email       = trim($_POST['email']           ?? '');
    $subjects    = trim($_POST['subjects']        ?? '');
    $phone       = trim($_POST['phone']           ?? '');
    $gender      = trim($_POST['gender']          ?? '');
    $emp_date    = trim($_POST['employment_date'] ?? '');

    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } else {
        $full_name = $first_name . ' ' . $last_name;
        $u = mysqli_prepare($conn, "UPDATE users SET full_name=?, email=? WHERE id=?");
        mysqli_stmt_bind_param($u, 'ssi', $full_name, $email, $data['user_id']);
        mysqli_stmt_execute($u); mysqli_stmt_close($u);

        $t = mysqli_prepare($conn, "UPDATE teachers SET subjects=?, phone=?, gender=?, employment_date=? WHERE id=?");
        mysqli_stmt_bind_param($t, 'ssssi', $subjects, $phone, $gender, $emp_date, $id);
        mysqli_stmt_execute($t); mysqli_stmt_close($t);

        $success = true;
        // Refresh
        $stmt2 = mysqli_prepare($conn,
            "SELECT u.id as user_id, u.full_name, u.email, t.staff_id,
                    t.subjects, t.phone, t.gender, t.employment_date
             FROM teachers t JOIN users u ON u.id=t.user_id WHERE t.id=?");
        mysqli_stmt_bind_param($stmt2, 'i', $id);
        mysqli_stmt_execute($stmt2);
        $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));
        mysqli_stmt_close($stmt2);
    }
}

$names = explode(' ', $data['full_name'], 2);
$first = $names[0]; $last = $names[1] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | EDIT TEACHER</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Edit Teacher</h2></div>
  <div class="portal-nav">
    <a href="staffDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>
<div class="page-wrapper-sm">

  <?php if ($success): ?>
  <div class="alert alert-success">✓ Teacher details updated successfully.</div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px">
      <h2 class="card-title" style="margin:0;padding:0">Editing: <?= htmlspecialchars($data['full_name']) ?></h2>
      <span class="badge badge-primary" style="font-size:14px;padding:6px 14px"><?= htmlspecialchars($data['staff_id']) ?></span>
    </div>

    <form method="POST">
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">First Name</label>
          <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($first) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Last Name</label>
          <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($last) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-control">
            <option value="">-- Select --</option>
            <option value="Male" <?= $data['gender']==='Male'?'selected':'' ?>>Male</option>
            <option value="Female" <?= $data['gender']==='Female'?'selected':'' ?>>Female</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Subject(s) Taught</label>
          <input type="text" name="subjects" class="form-control" value="<?= htmlspecialchars($data['subjects']) ?>" placeholder="e.g. Mathematics, Physics">
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($data['phone']) ?>" placeholder="+256 700 000 000">
        </div>
        <div class="form-group">
          <label class="form-label">Employment Date</label>
          <input type="date" name="employment_date" class="form-control" value="<?= htmlspecialchars($data['employment_date']) ?>">
        </div>
      </div>

      <div class="btn-row" style="margin-top:10px">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="staffDB.php" class="btn btn-gray">Cancel</a>
      </div>
    </form>
  </div>

</div>
<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
