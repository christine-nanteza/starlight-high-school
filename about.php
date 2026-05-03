<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | ABOUT</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>

<!-- PAGE HERO -->
<div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));padding:50px 24px;text-align:center">
  <div style="max-width:700px;margin:0 auto">
    <span class="hero-badge">About Us</span>
    <h1 style="font-family:'Poppins',sans-serif;font-size:36px;font-weight:700;color:var(--white);margin-top:14px">About Starlight High School</h1>
    <p style="color:rgba(255,255,255,0.8);margin-top:12px;font-size:15px">Learn about who we are, our leadership, our purpose and what makes us different.</p>
  </div>
</div>

<div class="page-wrapper">

  <!-- OVERVIEW -->
  <div class="card">
    <h2 class="card-title">Welcome to Starlight High School</h2>
    <div style="height:14px"></div>
    <p>Starlight High School is a private mixed day and boarding school committed to academic excellence, moral integrity and holistic education. We offer education from Senior One to Senior Six following the UNEB curriculum. Founded on the belief that every child deserves quality education, we provide a nurturing environment where students can grow academically, socially and morally.</p>
  </div>

  <!-- HEAD TEACHER MESSAGE -->
  <div class="card" style="border-left:4px solid var(--accent)">
    <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap">
      <div style="flex:1;min-width:250px">
        <h2 class="card-title">Message from the Head Teacher</h2>
        <div style="height:14px"></div>
        <p style="font-style:italic;font-size:16px;color:var(--text);line-height:1.9">"At Starlight High School, we believe that every child has unique potential and competency. Our goal is to nurture that potential by providing quality teaching, modern facilities and a supportive learning environment. Together, we strive for excellence in academics, sports and moral values."</p>
        <p style="margin-top:16px;font-weight:600;color:var(--primary)">— Mr. Joseph Kaggwa, Head Teacher</p>
      </div>
    </div>
  </div>

  <!-- MISSION VISION VALUES -->
  <div class="section-heading" style="margin-top:20px">
    <span class="label">Our Purpose</span>
    <h2>Mission, Vision &amp; Values</h2>
  </div>
  <div class="features-grid" style="margin-bottom:40px">
    <div class="feature-card gold-top">
      <div class="feature-icon">🎯</div>
      <h3>Our Mission</h3>
      <p>To provide quality, holistic education that nurtures academic excellence, moral integrity and practical skills for life and service.</p>
    </div>
    <div class="feature-card blue-top">
      <div class="feature-icon">👁️</div>
      <h3>Our Vision</h3>
      <p>To be a leading secondary school recognised for academic excellence, innovation and producing disciplined, responsible learners.</p>
    </div>
    <div class="feature-card green-top">
      <div class="feature-icon">⭐</div>
      <h3>Core Values</h3>
      <p><strong>Integrity</strong> · <strong>Discipline</strong> · <strong>Excellence</strong> · <strong>Respect</strong> · <strong>Responsibility</strong></p>
    </div>
  </div>

  <!-- LEADERSHIP -->
  <div class="card">
    <h2 class="card-title">Our Leadership Team</h2>
    <div style="height:14px"></div>
    <div class="form-grid-2">
      <div class="info-item" style="padding:20px;border-left-width:4px">
        <div class="info-label">Head Teacher</div>
        <div class="info-value" style="margin-bottom:6px">Mr. Joseph Kaggwa</div>
        <p style="font-size:13px;color:var(--text-muted)">M.Ed. Educational Management — Makerere University<br>Over 20 years of experience in secondary education.</p>
      </div>
      <div class="info-item" style="padding:20px;border-left-width:4px">
        <div class="info-label">Deputy Head Teacher</div>
        <div class="info-value" style="margin-bottom:6px">Mrs. Grace Nalubega</div>
        <p style="font-size:13px;color:var(--text-muted)">B.Ed. — Kyambogo University<br>15 years in curriculum development and student affairs.</p>
      </div>
    </div>
  </div>

  <!-- DEPARTMENTS -->
  <div class="card">
    <h2 class="card-title">Our Departments</h2>
    <div style="height:14px"></div>
    <div class="features-grid">
      <div class="feature-card" style="padding:22px">
        <div class="feature-icon" style="font-size:20px;width:42px;height:42px">🔬</div>
        <h3 style="font-size:15px">Sciences Department</h3>
        <p style="font-size:13px">Physics, Chemistry, Biology and Mathematics with a modern laboratory for practical learning.</p>
      </div>
      <div class="feature-card gold-top" style="padding:22px">
        <div class="feature-icon" style="font-size:20px;width:42px;height:42px">📚</div>
        <h3 style="font-size:15px">Arts Department</h3>
        <p style="font-size:13px">History, Geography, Divinity, Literature and Economics with focus on critical thinking.</p>
      </div>
      <div class="feature-card blue-top" style="padding:22px">
        <div class="feature-icon" style="font-size:20px;width:42px;height:42px">🌐</div>
        <h3 style="font-size:15px">Languages Department</h3>
        <p style="font-size:13px">English, Literature, Kiswahili and communication skills for national and global engagement.</p>
      </div>
      <div class="feature-card green-top" style="padding:22px">
        <div class="feature-icon" style="font-size:20px;width:42px;height:42px">💻</div>
        <h3 style="font-size:15px">Mathematics &amp; ICT</h3>
        <p style="font-size:13px">Strong Mathematics foundation complemented by a dedicated computer lab for ICT studies.</p>
      </div>
    </div>
  </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
