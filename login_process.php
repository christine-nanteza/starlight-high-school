<?php
// ============================================
// STARLIGHT HIGH SCHOOL — SECURE LOGIN HANDLER
// ============================================
session_start();
include "config.php";

// 1. Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// 2. Collect and sanitize inputs
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role']     ?? '');

// 3. Check all fields are filled
if (empty($email) || empty($password) || empty($role)) {
    header("Location: login.php?error=3");
    exit();
}

// 4. Validate role is one of the allowed values
$allowed_roles = ['student', 'teacher', 'admin'];
if (!in_array($role, $allowed_roles)) {
    header("Location: login.php?error=1");
    exit();
}

// 5. Look up user using a PREPARED STATEMENT (no SQL injection possible)
$stmt = mysqli_prepare($conn, "SELECT id, full_name, email, password, role, status FROM users WHERE email = ? AND role = ?");
mysqli_stmt_bind_param($stmt, "ss", $email, $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// 6. Check if user exists
if (mysqli_num_rows($result) !== 1) {
    header("Location: login.php?error=1");
    exit();
}

$user = mysqli_fetch_assoc($result);

// 7. Check account is active
if ($user['status'] !== 'active') {
    header("Location: login.php?error=2");
    exit();
}

// 8. Verify password using password_verify()
if (!password_verify($password, $user['password'])) {
    header("Location: login.php?error=1");
    exit();
}

// 9. Login successful — store session variables
session_regenerate_id(true);

$_SESSION['user_id']   = $user['id'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email']     = $user['email'];
$_SESSION['role']      = $user['role'];

// 10. Redirect based on role
if ($user['role'] === 'student') {
    header("Location: studentDB.php");
} elseif ($user['role'] === 'teacher') {
    header("Location: teacherDB.php");
} elseif ($user['role'] === 'admin') {
    header("Location: staffDB.php");
}

mysqli_stmt_close($stmt);
exit();
?>
