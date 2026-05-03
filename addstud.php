<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';
$success = false;
$error   = '';

function generate_reg_number($conn) {
    $year = date('Y');
    $prefix = "SHS/$year/";
    $result = mysqli_query($conn, "SELECT registration_number FROM students WHERE registration_number LIKE '$prefix%' ORDER BY registration_number DESC LIMIT 1");
    $next = ($row = mysqli_fetch_assoc($result)) ? intval(substr($row['registration_number'], -3)) + 1 : 1;
    return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
}

$auto_reg = generate_reg_number($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name   = trim($_POST['first_name']    ?? '');
    $last_name    = trim($_POST['last_name']      ?? '');
    $dob          = trim($_POST['date_of_birth']  ?? '');
    $gender       = trim($_POST['gender']         ?? '');
    $class        = trim($_POST['class']          ?? '');
    $reg_number   = $auto_reg;
    $email        = trim($_POST['email']          ?? '');
    $phone        = trim($_POST['phone']          ?? '');
    $student_type = trim($_POST['student_type']   ?? '');
    $admission    = trim($_POST['admission_date'] ?? '');
    $guardian     = trim($_POST['guardian_name']  ?? '');

    if (empty($first_name) || empty($last_name) || empty($class) || empty($email) || empty($gender)) {
        $error = 'Please fill in all required fields.';
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $error = 'That email is already registered in the system.';
        } else {
            mysqli_stmt_close($check);
            $hash = password_hash('starlight123', PASSWORD_BCRYPT);
            $full_name = $first_name . ' ' . $last_name;
            $stmt1 = mysqli_prepare($conn, "INSERT INTO users (full_name, email, password, role, status) VALUES (?, ?, ?, 'student', 'active')");
            mysqli_stmt_bind_param($stmt1, 'sss', $full_name, $email, $hash);
            if (mysqli_stmt_execute($stmt1)) {
                $user_id = mysqli_insert_id($conn);
                $stmt2 = mysqli_prepare($conn, "INSERT INTO students (user_id, registration_number, date_of_birth, gender, class, student_type, guardian_name, guardian_phone, admission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, 'issssssss', $user_id, $reg_number, $dob, $gender, $class, $student_type, $guardian, $phone, $admission);
                if (mysqli_stmt_execute($stmt2)) {
                    $success = true;
                    include_once 'mailer.php';
                    notify_account_created($email, $full_name, $reg_number, $class);
                    $auto_reg = generate_reg_number($conn);
                } else {
                    $del = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
                    mysqli_stmt_bind_param($del, 'i', $user_id);
                    mysqli_stmt_execute($del);
                    $error = 'Failed to save student record.';
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
<title>STARLIGHT HIGH SCHOOL | ADD STUDENT</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="portal-header">
  <div class="portal-header-left"><h1>Starlight High School</h1><h2>Add New Student</h2></div>
  <div class="portal-nav">
    <a href="staffDB.php">Dashboard</a>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<div class="page-wrapper-sm">

<?php if ($success): ?>
<div class="card">
  <h2 class="card-title">Student Added Successfully</h2>
  <div style="height:14px"></div>
  <div class="success-box">
    <h3>✓ <?= htmlspecialchars($first_name . ' ' . $last_name) ?> has been added</h3>
    <p><strong>Registration Number:</strong> <?= htmlspecialchars($reg_number) ?></p>
    <p><strong>Class:</strong> <?= htmlspecialchars($class) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
    <div class="highlight"><strong>Default Password:</strong> starlight123<br>Student logs in with email + registration number. Ask them to change password after first login.</div>
  </div>
  <div class="btn-row" style="margin-top:20px">
    <a href="addstud.php" class="btn btn-primary">Add Another Student</a>
    <a href="managefees.php?student_id=<?= $user_id ?>" class="btn btn-outline-primary">Add Fee Record</a>
    <a href="staffDB.php" class="btn btn-gray">Back to Dashboard</a>
  </div>
</div>

<?php else: ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form action="addstud.php" method="POST">

  <div class="card">
    <h2 class="card-title">Auto-Generated Registration Number</h2>
    <div style="height:14px"></div>
    <div class="reg-preview">
      <div>
        <div class="reg-label">Registration Number</div>
        <div class="reg-value"><?= htmlspecialchars($auto_reg) ?></div>
        <div class="reg-note">Assigned automatically — cannot be changed.</div>
      </div>
    </div>
  </div>

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
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-control" required>
          <option value="">-- Select --</option>
          <option value="Male" <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
        </select>
      </div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Academic Information</h2>
    <div style="height:14px"></div>
    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label">Class</label>
        <select name="class" class="form-control" required>
          <option value="">-- Select Class --</option>
          <?php foreach (['S1'=>'Senior One (S1)','S2'=>'Senior Two (S2)','S3'=>'Senior Three (S3)','S4'=>'Senior Four (S4)','S5'=>'Senior Five (S5)','S6'=>'Senior Six (S6)'] as $v=>$l): ?>
          <option value="<?=$v?>" <?=(($_POST['class']??'')===$v)?'selected':''?>><?=$l?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Student Type</label>
        <select name="student_type" class="form-control">
          <option value="">-- Select --</option>
          <option value="Day" <?=(($_POST['student_type']??'')==='Day')?'selected':''?>>Day Student</option>
          <option value="Boarding" <?=(($_POST['student_type']??'')==='Boarding')?'selected':''?>>Boarding Student</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Admission Date</label>
        <input type="date" name="admission_date" class="form-control" value="<?= htmlspecialchars($_POST['admission_date'] ?? date('Y-m-d')) ?>">
      </div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Login & Contact Information</h2>
    <div style="height:14px"></div>
    <div class="form-group">
      <label class="form-label">Email Address <span class="optional">(used to log in)</span></label>
      <input type="email" name="email" class="form-control" placeholder="student@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label">Guardian Name <span class="optional">(optional)</span></label>
        <input type="text" name="guardian_name" class="form-control" placeholder="Guardian full name" value="<?= htmlspecialchars($_POST['guardian_name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Guardian Phone <span class="optional">(optional)</span></label>
        <input type="tel" name="phone" class="form-control" placeholder="+256 700 000 000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div>
    </div>
    <div class="btn-row">
      <button type="submit" class="btn btn-primary">Add Student</button>
      <a href="staffDB.php" class="btn btn-gray">Cancel</a>
    </div>
  </div>

</form>
<?php endif; ?>
</div>

<div class="portal-footer"><strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.</div>
</body>
</html>
