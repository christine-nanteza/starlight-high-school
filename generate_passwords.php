<?php
// ============================================
// STARLIGHT HIGH SCHOOL — PASSWORD HASH TOOL
// ============================================
// Use this page to generate bcrypt hashes for
// your user passwords before inserting them
// into the database.
//
// HOW TO USE:
// 1. Open this page in your browser
// 2. Type in the password you want to hash
// 3. Copy the hash shown
// 4. Paste it into the users table in phpMyAdmin
//
// DELETE THIS FILE from your server once you
// are done — it should not be publicly accessible.
// ============================================

$hash = '';
$password_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
    $password_input = $_POST['password'];
    $hash = password_hash($password_input, PASSWORD_BCRYPT);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Hash Generator | Starlight</title>
    <style>
        body { font-family: Arial, sans-serif; background: #F9FAFB; padding: 40px 20px; color: #1F2937; }
        .card { background: white; max-width: 600px; margin: auto; padding: 35px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        h2 { color: #2563EB; margin-bottom: 8px; }
        .warning { background: #FEF9C3; border-left: 4px solid #EAB308; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 15px; margin-bottom: 16px; }
        button { background: #2563EB; color: white; border: none; padding: 12px 28px; border-radius: 30px; font-weight: bold; cursor: pointer; font-size: 15px; }
        button:hover { background: #1D4ED8; }
        .result { margin-top: 24px; background: #F0FDF4; border-left: 4px solid #16A34A; padding: 16px; border-radius: 8px; }
        .result p { font-size: 14px; margin-bottom: 8px; }
        .hash { font-family: monospace; font-size: 13px; word-break: break-all; background: #DCFCE7; padding: 10px; border-radius: 6px; }
        .sql { font-family: monospace; font-size: 13px; word-break: break-all; background: #EFF6FF; padding: 10px; border-radius: 6px; margin-top: 10px; color: #1E40AF; }
    </style>
</head>
<body>
<div class="card">
    <h2>Password Hash Generator</h2>
    <div class="warning">
        ⚠️ <strong>Delete this file after use.</strong> Do not leave it on a live server.
    </div>

    <form method="POST">
        <label for="password">Enter a plain text password:</label>
        <input type="text" name="password" id="password"
               value="<?= htmlspecialchars($password_input) ?>"
               placeholder="e.g. MySecurePass@123">
        <button type="submit">Generate Hash</button>
    </form>

    <?php if ($hash): ?>
    <div class="result">
        <p><strong>Password entered:</strong> <?= htmlspecialchars($password_input) ?></p>
        <p><strong>Bcrypt hash (copy this into the database):</strong></p>
        <div class="hash"><?= htmlspecialchars($hash) ?></div>
        <p style="margin-top:12px"><strong>Ready-to-use SQL UPDATE (replace email):</strong></p>
        <div class="sql">UPDATE users SET password = '<?= htmlspecialchars($hash) ?>' WHERE email = 'user@example.com';</div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
