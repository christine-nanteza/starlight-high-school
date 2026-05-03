<?php
// ============================================
// STARLIGHT HIGH SCHOOL — RESET ALL PASSWORDS
// Opens in browser, resets ALL users to starlight123
// DELETE THIS FILE after use!
// ============================================
include 'config.php';

$done = false;
$count = 0;

if (isset($_POST['confirm'])) {
    $hash = password_hash('starlight123', PASSWORD_BCRYPT);
    $result = mysqli_query($conn, "UPDATE users SET password = '$hash'");
    if ($result) {
        $count = mysqli_affected_rows($conn);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset All Passwords | Starlight</title>
<link rel="stylesheet" href="style.css">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:var(--bg)">
<div class="card" style="max-width:500px;width:100%">

<?php if ($done): ?>
  <div class="alert alert-success">
    ✅ Done! <strong><?= $count ?> user(s)</strong> have been reset.<br><br>
    Everyone can now log in with:<br>
    <strong style="font-size:20px;color:var(--primary)">starlight123</strong>
  </div>
  <div class="alert alert-warning">⚠️ Delete this file from your starlight folder now!</div>
  <a href="login.php" class="btn btn-primary btn-full">Go to Login</a>

<?php else: ?>
  <h2 class="card-title">Reset All Passwords</h2>
  <div style="height:14px"></div>
  <div class="alert alert-warning">
    ⚠️ This will reset <strong>ALL</strong> student, teacher and admin passwords to:<br>
    <strong style="font-size:20px;color:var(--primary)">starlight123</strong>
  </div>
  <form method="POST">
    <button type="submit" name="confirm" class="btn btn-danger btn-full btn-lg">
      Reset All Passwords to starlight123
    </button>
  </form>
  <div style="margin-top:12px">
    <a href="login.php" class="btn btn-gray btn-full">Cancel</a>
  </div>
<?php endif; ?>

</div>
</body>
</html>
