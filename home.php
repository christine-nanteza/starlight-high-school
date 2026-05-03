<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | HOME</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'nav.php'; ?>

<!-- HERO -->
<section class="hero-section">
  <div class="hero-content">
    <span class="hero-badge">Welcome to Starlight High School</span>
    <h1 class="hero-title">Nurturing <span>Excellence</span>,<br>Shaping Futures</h1>
    <p class="hero-subtitle">A private mixed day and boarding secondary school committed to academic excellence, moral integrity and holistic education under the UNEB curriculum.</p>
    <div class="hero-actions">
      <a href="apply.php" class="btn btn-accent btn-lg">Apply for Admission</a>
      <a href="about.php" class="btn btn-outline btn-lg">Learn More</a>
    </div>
  </div>
</section>

<!-- SLIDESHOW -->
<div class="slideshow-container">
  <div class="slide fade"><img src="images/hh.png"  alt="School Campus"></div>
  <div class="slide fade"><img src="images/hh1.png" alt="Classroom"></div>
  <div class="slide fade"><img src="images/hh2.png" alt="Students Learning"></div>
  <div class="slide fade"><img src="images/hh3.png" alt="School Life"></div>
  <div class="slide fade"><img src="images/hh4.png" alt="School Events"></div>
</div>

<!-- WHY STARLIGHT -->
<div class="section-wrap" style="background:var(--white)">
  <div class="page-wrapper">
    <div class="section-heading">
      <span class="label">Why Choose Us</span>
      <h2>The Starlight Difference</h2>
      <p>We go beyond academics to develop confident, disciplined and well-rounded learners ready for life.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card gold-top">
        <div class="feature-icon">🏆</div>
        <h3>Academic Excellence</h3>
        <p>Consistent top performers in national UNEB examinations at both O-Level and A-Level with dedicated, qualified teachers.</p>
      </div>
      <div class="feature-card blue-top">
        <div class="feature-icon">🎓</div>
        <h3>Holistic Education</h3>
        <p>Beyond academics we invest in sports, music, clubs and community service to develop the complete student.</p>
      </div>
      <div class="feature-card green-top">
        <div class="feature-icon">🛡️</div>
        <h3>Safe Environment</h3>
        <p>A disciplined, respectful and inclusive school community where every student feels valued, safe and supported.</p>
      </div>
      <div class="feature-card" style="border-color:var(--primary)">
        <div class="feature-icon">💻</div>
        <h3>Modern Facilities</h3>
        <p>Modern science laboratory, computer lab, sports grounds and well-equipped classrooms for quality learning.</p>
      </div>
    </div>
  </div>
</div>

<!-- PURPOSE -->
<div class="section-wrap">
  <div class="page-wrapper">
    <div class="section-heading">
      <span class="label">Our Purpose</span>
      <h2>Mission, Vision &amp; Values</h2>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🎯</div>
        <h3>Our Mission</h3>
        <p>To provide quality, holistic education that nurtures academic excellence, moral integrity and practical skills for life and service.</p>
      </div>
      <div class="feature-card gold-top">
        <div class="feature-icon">👁️</div>
        <h3>Our Vision</h3>
        <p>To be a leading secondary school recognised for academic excellence, innovation and producing disciplined, responsible learners.</p>
      </div>
      <div class="feature-card green-top">
        <div class="feature-icon">⭐</div>
        <h3>Core Values</h3>
        <p>Integrity · Discipline · Excellence · Respect · Responsibility — the five pillars that guide everything we do at Starlight.</p>
      </div>
    </div>
  </div>
</div>

<!-- ADMISSIONS CTA -->
<div class="section-wrap" style="background:var(--primary);padding:60px 24px">
  <div class="page-wrapper" style="text-align:center">
    <span class="hero-badge">Admissions Open</span>
    <h2 style="font-family:'Poppins',sans-serif;font-size:34px;font-weight:700;color:var(--white);margin:16px 0">Enrol Your Child Today</h2>
    <p style="color:rgba(255,255,255,0.8);font-size:16px;max-width:550px;margin:0 auto 32px">Give your child a strong academic foundation and a bright future. Admissions open for Term 1, Term 2 and Term 3.</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap">
      <a href="apply.php"    class="btn btn-accent btn-lg">Apply Now</a>
      <a href="contact.php" class="btn btn-outline btn-lg">Contact Us</a>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<script>
let idx = 0;
const slides = document.querySelectorAll('.slide');
function showSlides() {
  slides.forEach(s => s.style.display = 'none');
  idx = (idx >= slides.length) ? 0 : idx;
  slides[idx].style.display = 'block';
  slides[idx].classList.add('fade');
  idx++;
  setTimeout(showSlides, 4500);
}
showSlides();
</script>
</body>
</html>
