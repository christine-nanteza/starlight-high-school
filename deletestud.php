<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: staffDB.php"); exit(); }

// Get student user_id first
$stmt = mysqli_prepare($conn, "SELECT s.id, u.id as user_id FROM students s JOIN users u ON u.id=s.user_id WHERE s.id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) { header("Location: staffDB.php"); exit(); }

$user_id = $row['user_id'];

// Delete all related records then the student and user
mysqli_query($conn, "DELETE FROM marks WHERE student_id=$id");
mysqli_query($conn, "DELETE FROM fees WHERE student_id=$id");
mysqli_query($conn, "DELETE FROM students WHERE id=$id");
mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");

header("Location: staffDB.php?msg=student_deleted");
exit();
