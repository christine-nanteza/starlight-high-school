<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
include 'config.php';

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn,
    "SELECT u.full_name, u.email,
            s.registration_number, s.date_of_birth, s.gender,
            s.class, s.student_type, s.guardian_name,
            s.guardian_phone, s.admission_date
     FROM users u
     JOIN students s ON s.user_id = u.id
     WHERE u.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$student) $student = [];

$pw_success = false;
$pw_error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($current) || empty($new) || empty($confirm)) {
        $pw_error = 'Please fill in all password fields.';
    } elseif (strlen($new) < 6) {
        $pw_error = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $pw_error = 'New passwords do not match.';
    } else {
        $s = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
        mysqli_stmt_bind_param($s, 'i', $user_id);
        mysqli_stmt_execute($s);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        mysqli_stmt_close($s);

        if (!password_verify($current, $row['password'])) {
            $pw_error = 'Current password is incorrect.';
        } else {
            $new_hash = password_hash($new, PASSWORD_BCRYPT);
            $upd = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($upd, 'si', $new_hash, $user_id);
            mysqli_stmt_execute($upd) ? $pw_success = true : $pw_error = 'Failed to update password.';
            mysqli_stmt_close($upd);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | STUDENT PROFILE</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="portal-header">
  <div class="portal-header-left">
    <h1>Starlight High School</h1>
    <h2>Student Profile</h2>
  </div>
  <div class="portal-nav">
    <a href="studentDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<div class="page-wrapper-sm">

  <!-- PERSONAL INFORMATION -->
  <div class="card">
    <h2 class="card-title">Personal Information</h2>
    <div style="height:14px"></div>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Full Name</div>
        <div class="info-value"><?= !empty($student['full_name']) ? htmlspecialchars($student['full_name']) : '—' ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Registration Number</div>
        <div class="info-value"><?= !empty($student['registration_number']) ? htmlspecialchars($student['registration_number']) : '—' ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Date of Birth</div>
        <div class="info-value"><?= !empty($student['date_of_birth']) ? date('d F Y', strtotime($student['date_of_birth'])) : '—' ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Gender</div>
        <div class="info-value"><?= !empty($student['gender']) ? htmlspecialchars($student['gender']) : '—' ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Email Address</div>
        <div class="info-value"><?= !empty($student['email']) ? htmlspecialchars($student['email']) : '—' ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Student Type</div>
        <div class="info-value"><?= !empty($student['student_type']) ? htmlspecialchars($student['student_type']) : '—' ?></div>
      </div>
    </div>
  </div>

  <!-- ACADEMIC INFORMATION -->
  <div class="card">
    <h2 class="card-title">Academic Information</h2>
    <div style="height:14px"></div>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Class</div>
        <div class="info-value"><?= !empty($student['class']) ? htmlspecialchars($student['class']) : '—' ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Admission Date</div>
        <div class="info-value"><?= !empty($student['admission_date']) ? date('d F Y', strtotime($student['admission_date'])) : '—' ?></div>
      </div>
    </div>
  </div>

  <!-- GUARDIAN INFORMATION -->
  <div class="card">
    <h2 class="card-title">Guardian Information</h2>
    <div style="height:14px"></div>
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Guardian Name</div>
        <div class="info-value"><?= !empty($student['guardian_name']) ? htmlspecialchars($student['guardian_name']) : '—' ?></div>
      </div>
      <div class="info-item">
        <div class="info-label">Guardian Phone</div>
        <div class="info-value"><?= !empty($student['guardian_phone']) ? htmlspecialchars($student['guardian_phone']) : '—' ?></div>
      </div>
    </div>
  </div>

  <!-- CHANGE PASSWORD -->
  <div class="card">
    <h2 class="card-title">Change Password</h2>
    <div style="height:14px"></div>

    <?php if ($pw_success): ?>
    <div class="alert alert-success">✓ Password updated successfully.</div>
    <?php endif; ?>
    <?php if (!empty($pw_error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($pw_error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Current Password</label>
        <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
      </div>
      <div class="form-grid-2">
        <div class="form-group">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" placeholder="At least 6 characters">
        </div>
        <div class="form-group">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password">
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
  </div>

</div>

<div class="portal-footer">
  <strong>Starlight High School</strong> | Private Mixed Secondary School | UNEB Curriculum<br>
  &copy; <?= date('Y') ?> Starlight High School. All rights reserved.
</div>

</body>
</html>
