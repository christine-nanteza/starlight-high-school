<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | LOGIN</title>
<link rel="stylesheet" href="style.css">
<style>
.login-wrap{min-height:calc(100vh - 72px);display:flex;align-items:center;justify-content:center;padding:40px 20px;background:var(--bg);}
.login-card{background:var(--white);border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:48px 44px;width:100%;max-width:460px;}
.login-logo{text-align:center;margin-bottom:28px;}
.login-logo img{width:64px;height:64px;object-fit:contain;}
.login-logo h2{font-family:'Poppins',sans-serif;font-size:22px;font-weight:700;color:var(--primary);margin-top:10px;}
.login-logo p{font-size:13px;color:var(--text-muted);}
.divider{border:none;border-top:1px solid var(--border);margin:20px 0;}
</style>
</head>
<body>


<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">
      <img src="images/logo.png" alt="Starlight Logo">
      <h2>Welcome Back</h2>
      <p>Sign in to your Starlight High School account</p>
    </div>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
      <?php $errors=['1'=>'Invalid email or password. Please try again.','2'=>'Your account is inactive. Contact the school administration.','3'=>'Please fill in all fields.'];
      echo $errors[$_GET['error']] ?? 'An error occurred. Please try again.'; ?>
    </div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
      <div class="form-group">
        <label class="form-label">Login As</label>
        <select name="role" class="form-control" required>
          <option value="">-- Select your role --</option>
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
          <option value="admin">Staff / Admin</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:6px">Sign In</button>
    </form>

    <hr class="divider">
    <p style="text-align:center;font-size:13px;color:var(--text-muted)">
      Forgot your password? <a href="contact.php" style="color:var(--primary);font-weight:600">Contact Administration</a>
    </p>
  </div>
</div>


</body>
</html>