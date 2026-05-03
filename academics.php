<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | ACADEMICS</title>
<link rel="stylesheet" href="style.css">
<style>
.subjects-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;margin-top:16px;}
.subject-item{background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;font-size:14px;font-weight:500;color:var(--primary);display:flex;align-items:center;gap:8px;}
.subject-item::before{content:'📖';font-size:14px;}
</style>
</head>
<body>
<?php include 'nav.php'; ?>

<div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));padding:50px 24px;text-align:center">
  <div style="max-width:700px;margin:0 auto">
    <span class="hero-badge">Academics</span>
    <h1 style="font-family:'Poppins',sans-serif;font-size:36px;font-weight:700;color:var(--white);margin-top:14px">Our Academic Program</h1>
    <p style="color:rgba(255,255,255,0.8);margin-top:12px">Following the UNEB curriculum for both O-Level and A-Level, designed to nurture intellectual growth and practical skills.</p>
  </div>
</div>

<div class="page-wrapper-md">

  <div class="card">
    <h2 class="card-title">O-Level Subjects (S1 – S4)</h2>
    <div style="height:14px"></div>
    <p style="color:var(--text-muted);margin-bottom:4px">Students in Senior One to Senior Four study the following subjects under the UNEB O-Level curriculum:</p>
    <div class="subjects-list">
      <?php foreach(['Mathematics','English Language','Biology','Chemistry','Physics','Geography','History','Christian Religious Education','Islamic Religious Education','Computer Studies','Fine Art','Literature in English','Kiswahili','Agriculture'] as $s): ?>
      <div class="subject-item"><?= $s ?></div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">A-Level Subjects (S5 – S6)</h2>
    <div style="height:14px"></div>
    <p style="color:var(--text-muted);margin-bottom:20px">Students choose a combination of Sciences or Arts subjects:</p>
    <div class="form-grid-2">
      <div>
        <h3 style="font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;color:var(--primary);margin-bottom:12px;display:flex;align-items:center;gap:8px"><span style="background:var(--bg);padding:4px 10px;border-radius:20px;font-size:12px;border:1px solid var(--border)">🔬 Sciences</span></h3>
        <div class="subjects-list" style="grid-template-columns:1fr">
          <?php foreach(['Mathematics','Physics','Biology','Chemistry','Economics'] as $s): ?>
          <div class="subject-item"><?= $s ?></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div>
        <h3 style="font-family:'Poppins',sans-serif;font-size:15px;font-weight:600;color:var(--primary);margin-bottom:12px;display:flex;align-items:center;gap:8px"><span style="background:var(--bg);padding:4px 10px;border-radius:20px;font-size:12px;border:1px solid var(--border)">📚 Arts</span></h3>
        <div class="subjects-list" style="grid-template-columns:1fr">
          <?php foreach(['History','Geography','Divinity','Economics','Literature in English'] as $s): ?>
          <div class="subject-item"><?= $s ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <h2 class="card-title">Academic Departments</h2>
    <div style="height:14px"></div>
    <div class="features-grid">
      <?php foreach([['🔬','Science Department','Physics, Chemistry, Biology — with a modern laboratory for practical work.'],['📚','Arts Department','History, Geography, Divinity, Literature and Economics.'],['💻','Mathematics & ICT','Strong maths foundation from S1–S6 with a dedicated computer lab.'],['🌐','Languages Department','English, Literature, Kiswahili and communication skills.'],['🎨','Technical & Creative Arts','Fine Art, Agriculture and vocational skills for creative learners.']] as [$icon,$title,$desc]): ?>
      <div class="feature-card" style="padding:22px">
        <div class="feature-icon" style="font-size:20px;width:42px;height:42px"><?=$icon?></div>
        <h3 style="font-size:15px"><?=$title?></h3>
        <p style="font-size:13px"><?=$desc?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div style="background:var(--primary);border-radius:var(--radius-lg);padding:36px;text-align:center;color:var(--white)">
    <h2 style="font-family:'Poppins',sans-serif;font-size:24px;font-weight:700;margin-bottom:10px">Ready to Join Starlight?</h2>
    <p style="color:rgba(255,255,255,0.8);margin-bottom:24px">Apply for admission today and start your journey towards academic excellence.</p>
    <a href="apply.php" class="btn btn-accent">Apply Now</a>
  </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
