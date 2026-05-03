<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';
$success = false; $error = '';

function generate_staff_id($conn) {
    $year = date('Y'); $prefix = "SHS/TR/$year/";
    $result = mysqli_query($conn, "SELECT staff_id FROM teachers WHERE staff_id LIKE '$prefix%' ORDER BY staff_id DESC LIMIT 1");
    $next = ($row = mysqli_fetch_assoc($result)) ? intval(substr($row['staff_id'], -3)) + 1 : 1;
    return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
}
$auto_staff_id = generate_staff_id($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $subjects = trim($_POST['subjects'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $emp_date = trim($_POST['employment_date'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $staff_id = $auto_staff_id;

    if (empty($first_name) || empty($last_name) || empty($email) || empty($gender)) {
        $error = 'Please fill in all required fields.';
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email=?");
        mysqli_stmt_bind_param($check, 's', $email); mysqli_stmt_execute($check); mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) { $error = 'That email is already registered.'; }
        else {
            mysqli_stmt_close($check);
            $hash = password_hash('starlight123', PASSWORD_BCRYPT);
            $full_name = $first_name . ' ' . $last_name;
            $stmt1 = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'teacher', 'active')");
            mysqli_stmt_bind_param($stmt1, 'sss', $full_name, $email, $hash);
            if (mysqli_stmt_execute($stmt1)) {
                $user_id = mysqli_insert_id($conn);
                $stmt2 = mysqli_prepare($conn, "INSERT INTO teachers (user_id, staff_id, subjects, phone, employment_date, gender) VALUES (?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, 'isssss', $user_id, $staff_id, $subjects, $phone, $emp_date, $gender);
                if (mysqli_stmt_execute($stmt2)) {
                    $success = true;
                    $auto_staff_id = generate_staff_id($conn);
                } else {
                    $del = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
                    mysqli_stmt_bind_param($del, 'i', $user_id); mysqli_stmt_execute($del);
                    $error = 'Failed to save teacher record.';
                }
                mysqli_stmt_close($stmt2);
            } else { $error = 'Failed to create login account.'; }
            mysqli_stmt_close($stmt1);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | ADD TEACHER</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Add New Teacher</h2></div>
  <div class="portal-nav">
    <a href="staffDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>
<div class="page-wrapper-sm">

<?php if ($success): ?>
<div class="card">
  <h2 class="card-title">Teacher Added Successfully</h2>
  <div style="height:14px"></div>
  <div class="success-box">
    <h3>✓ <?= htmlspecialchars($first_name . ' ' . $last_name) ?> has been added</h3>
    <p><strong>Staff ID:</strong> <?= htmlspecialchars($staff_id) ?></p>
    <p><strong>Subjects:</strong> <?= htmlspecialchars($subjects) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
    <div class="highlight"><strong>Default Password:</strong> starlight123<br>Default password is: starlight123 — ask them to change it after first login.</div>
  </div>
  <div class="btn-row" style="margin-top:20px">
    <a href="addtr.php" class="btn btn-primary">Add Another Teacher</a>
    <a href="staffDB.php" class="btn btn-gray">Back to Dashboard</a>
  </div>
</div>

<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
  <h2 class="card-title">Auto-Generated Staff ID</h2>
  <div style="height:14px"></div>
  <div class="reg-preview">
    <div>
      <div class="reg-label">Staff ID</div>
      <div class="reg-value"><?= htmlspecialchars($auto_staff_id) ?></div>
      <div class="reg-note">Assigned automatically — cannot be changed.</div>
    </div>
  </div>
</div>

<form action="addtr.php" method="POST">

  <div class="card">
    <h2 class="card-title">Personal Information</h2>
    <div style="height:14px"></div>
    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" placeholder="First name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" placeholder="Last name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-control" required>
          <option value="">-- Select --</option>
          <option value="Male" <?=(($_POST['gender']??'')==='Male')?'selected':''?>>Male</option>
          <option value="Female" <?=(($_POST['gender']??'')==='Female')?'selected':''?>>Female</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Phone <span class="optional">(optional)</span></label>
        <input type="tel" name="phone" class="form-control" placeholder="+256 700 000 000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Employment Information</h2>
    <div style="height:14px"></div>
    <div class="form-group">
      <label class="form-label">Subject(s) Taught</label>
      <input type="text" name="subjects" class="form-control" placeholder="e.g. Mathematics, Physics" value="<?= htmlspecialchars($_POST['subjects'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label class="form-label">Date Employed</label>
      <input type="date" name="employment_date" class="form-control" value="<?= htmlspecialchars($_POST['employment_date'] ?? '') ?>">
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Login Information</h2>
    <div style="height:14px"></div>
    <div class="form-group">
      <label class="form-label">Email Address <span class="optional">(used to log in)</span></label>
      <input type="email" name="email" class="form-control" placeholder="teacher@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="btn-row">
      <button type="submit" class="btn btn-primary">Add Teacher</button>
      <a href="staffDB.php" class="btn btn-gray">Cancel</a>
    </div>
  </div>

</form>
<?php endif; ?>

</div>
<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
