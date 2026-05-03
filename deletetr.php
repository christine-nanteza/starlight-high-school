<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php"); exit();
}
include 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: staffDB.php"); exit(); }

// Get teacher user_id first
$stmt = mysqli_prepare($conn, "SELECT t.id, u.id as user_id FROM teachers t JOIN users u ON u.id=t.user_id WHERE t.id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$row) { header("Location: staffDB.php"); exit(); }

$user_id = $row['user_id'];

// Delete marks submitted by this teacher, then teacher record and user
mysqli_query($conn, "DELETE FROM marks WHERE teacher_id=$id");
mysqli_query($conn, "DELETE FROM teachers WHERE id=$id");
mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");

header("Location: staffDB.php?msg=teacher_deleted");
exit();
