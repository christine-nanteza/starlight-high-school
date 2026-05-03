<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php"); exit();
}
$full_name = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | TEACHER DASHBOARD</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="portal-header">
  <div class="portal-header-left">
    <h1>Starlight High School</h1>
    <h2>Teacher Portal</h2>
  </div>
  <div class="portal-nav">
    <a href="home.php">🏠 Home</a>
    <a href="logout.php" class="logout">⬆ Logout</a>
  </div>
</div>

<div class="page-wrapper-md">

  <div class="welcome-banner">
    <h2>Welcome, <span class="accent-text"><?= htmlspecialchars($full_name) ?></span> 👋</h2>
    <p>Manage student marks, view reports and update your profile from here.</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-label">Your Portal</div>
      <div class="stat-num" style="font-size:18px;margin-top:4px">Teacher</div>
    </div>
    <div class="stat-card green">
      <div class="stat-label">Status</div>
      <div class="stat-num" style="font-size:18px;margin-top:4px">Active</div>
    </div>
    <div class="stat-card gold">
      <div class="stat-label">Academic Year</div>
      <div class="stat-num" style="font-size:18px;margin-top:4px"><?= date('Y') ?></div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Teacher Menu</h2>
    <div style="height:14px"></div>
    <div class="menu-grid">
      <a href="trprofile.php" class="menu-card">
        <span class="menu-icon">👤</span>
        <span class="menu-label">My Profile</span>
      </a>
      <a href="marks.php" class="menu-card">
        <span class="menu-icon">✏️</span>
        <span class="menu-label">Enter Marks</span>
      </a>
      <a href="viewreport.php" class="menu-card">
        <span class="menu-icon">📊</span>
        <span class="menu-label">View Reports</span>
      </a>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Notifications</h2>
    <div style="height:14px"></div>
    <div class="notification-empty">📢 No new notifications at this time.</div>
  </div>

</div>

<div class="portal-footer">
  <strong>Starlight High School</strong> | &copy; <?= date('Y') ?> All rights reserved.
</div>
</body>
</html>
