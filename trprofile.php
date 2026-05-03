<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}
include 'config.php';

$user_id = $_SESSION['user_id'];

// Fetch teacher data
$stmt = mysqli_prepare($conn,
    "SELECT u.full_name, u.email,
            t.staff_id, t.subjects, t.phone, t.gender, t.employment_date
     FROM users u JOIN teachers t ON t.user_id = u.id
     WHERE u.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$teacher = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Password change
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
            $hash = password_hash($new, PASSWORD_BCRYPT);
            $upd  = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($upd, 'si', $hash, $user_id);
            mysqli_stmt_execute($upd) ? $pw_success = true : $pw_error = 'Failed to update. Please try again.';
            mysqli_stmt_close($upd);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STARLIGHT HIGH SCHOOL | TEACHER PROFILE</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:"Segoe UI",Arial,sans-serif; }
        body { background:#f4f6f9; color:#333; line-height:1.6; }
        header { background:#1e3a5f; color:#fff; padding:20px 30px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
        header h1 { font-size:24px; } header h2 { font-size:15px; font-weight:normal; opacity:.85; }
        nav a { color:#fff; text-decoration:none; margin-left:15px; font-weight:500; }
        nav a:hover { text-decoration:underline; }
        main { max-width:1000px; margin:30px auto; padding:0 20px; display:grid; grid-template-columns:1fr 1fr; gap:22px; }
        section { background:white; padding:25px 28px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,.07); }
        section.full { grid-column:1/-1; }
        section h2 { color:#1e3a5f; font-size:18px; margin-bottom:20px; position:relative; padding-bottom:8px; }
        section h2::after { content:""; position:absolute; left:0; bottom:0; width:55px; height:3px; background:#f4b400; border-radius:3px; }
        .info-item { background:#f1f5ff; padding:13px 16px; border-radius:8px; border-left:4px solid #1e3a5f; margin-bottom:12px; }
        .info-item .lbl { font-size:11px; font-weight:bold; color:#6b7280; text-transform:uppercase; }
        .info-item .val { font-size:15px; font-weight:600; color:#1e3a5f; margin-top:2px; }
        .error-msg { background:#fef2f2; border-left:4px solid #ef4444; color:#b91c1c; padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:16px; }
        .success-msg { background:#f0fdf4; border-left:4px solid #16a34a; color:#15803d; padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:16px; }
        .form-group { margin-bottom:16px; }
        label { display:block; font-weight:600; font-size:14px; margin-bottom:5px; color:#374151; }
        input[type="password"] { width:100%; padding:10px 13px; border:1px solid #cbd5e1; border-radius:7px; font-size:14px; background:#f8fafc; }
        input[type="password"]:focus { outline:none; border-color:#1e3a5f; box-shadow:0 0 0 3px rgba(30,41,59,.1); }
        button { padding:11px 28px; background:#1e3a5f; color:white; border:none; border-radius:7px; font-size:15px; font-weight:600; cursor:pointer; transition:background .2s; }
        button:hover { background:#162d4a; }
        footer { background:#1e3a5f; color:white; text-align:center; padding:18px; margin-top:40px; font-size:13px; grid-column:1/-1; }
        @media(max-width:700px){ main{grid-template-columns:1fr;} section.full{grid-column:1;} }
    </style>
</head>
<body>
<header>
    <div><h1>Starlight High School</h1><h2>Teacher Profile</h2></div>
    <nav><a href="teacherDB.php">Dashboard</a> <a href="logout.php">Logout</a></nav>
</header>
<main>
    <!-- Personal Info -->
    <section>
        <h2>Personal Information</h2>
        <div class="info-item"><div class="lbl">Full Name</div><div class="val"><?=htmlspecialchars($teacher['full_name']??'—')?></div></div>
        <div class="info-item"><div class="lbl">Staff ID</div><div class="val"><?=htmlspecialchars($teacher['staff_id']??'—')?></div></div>
        <div class="info-item"><div class="lbl">Gender</div><div class="val"><?=htmlspecialchars($teacher['gender']??'—')?></div></div>
        <div class="info-item"><div class="lbl">Phone</div><div class="val"><?=htmlspecialchars($teacher['phone']??'—')?></div></div>
    </section>

    <!-- Employment Info -->
    <section>
        <h2>Employment Information</h2>
        <div class="info-item"><div class="lbl">Email</div><div class="val"><?=htmlspecialchars($teacher['email']??'—')?></div></div>
        <div class="info-item"><div class="lbl">Subjects</div><div class="val"><?=htmlspecialchars($teacher['subjects']??'—')?></div></div>
        <div class="info-item"><div class="lbl">Date Employed</div>
            <div class="val"><?=!empty($teacher['employment_date'])?date('d F Y',strtotime($teacher['employment_date'])):'—'?></div>
        </div>
    </section>

    <!-- Change Password -->
    <section class="full">
        <h2>Change Password</h2>
        <?php if ($pw_success): ?><div class="success-msg">Password updated successfully.</div><?php endif; ?>
        <?php if (!empty($pw_error)): ?><div class="error-msg"><?=htmlspecialchars($pw_error)?></div><?php endif; ?>
        <form method="POST" style="max-width:400px">
            <div class="form-group"><label>Current Password</label><input type="password" name="current_password" placeholder="Enter current password"></div>
            <div class="form-group"><label>New Password</label><input type="password" name="new_password" placeholder="At least 6 characters"></div>
            <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" placeholder="Repeat new password"></div>
            <button type="submit">Update Password</button>
        </form>
    </section>
</main>
<footer><p><strong>Starlight High School</strong> | &copy; 2026 All rights reserved.</p></footer>
</body>
</html>
