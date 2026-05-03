<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | ADMISSIONS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>

<div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));padding:50px 24px;text-align:center">
  <div style="max-width:700px;margin:0 auto">
    <span class="hero-badge">Admissions</span>
    <h1 style="font-family:'Poppins',sans-serif;font-size:36px;font-weight:700;color:var(--white);margin-top:14px">Join Starlight High School</h1>
    <p style="color:rgba(255,255,255,0.8);margin-top:12px">We welcome applications for both day and boarding students in Senior One through Senior Six.</p>
  </div>
</div>

<div class="page-wrapper-md">

  <!-- REQUIREMENTS -->
  <div class="card" id="requirements" style="border-left:4px solid var(--accent)">
    <h2 class="card-title">Admission Requirements</h2>
    <div style="height:14px"></div>
    <div style="display:flex;flex-direction:column;gap:10px">
      <?php foreach([
        ['📋','Completed primary school education (PLE / Primary 7)'],
        ['📄','Original academic transcripts and report cards'],
        ['🪪','Birth Certificate or proof of age'],
        ['📸','Passport-sized photographs (2 copies)'],
        ['✉️','Recommendation letter from previous school (optional)'],
      ] as [$icon,$req]): ?>
      <div style="display:flex;align-items:center;gap:14px;background:var(--bg);padding:14px 18px;border-radius:var(--radius-sm)">
        <span style="font-size:20px"><?=$icon?></span>
        <span style="font-size:15px;color:var(--text)"><?=$req?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- FEES -->
  <div class="card" id="fees" style="border-left:4px solid var(--success)">
    <h2 class="card-title">School Fees</h2>
    <div style="height:14px"></div>
    <p style="color:var(--text-muted);margin-bottom:20px">Fees are charged per term. Boarding fees are separate from day students.</p>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr><th>Fee Type</th><th>Day Students</th><th>Boarding Students</th></tr>
        </thead>
        <tbody>
          <tr><td>Tuition Fee</td><td>UGX 800,000</td><td>UGX 1,500,000</td></tr>
          <tr><td>Examination Fee</td><td>UGX 50,000</td><td>UGX 50,000</td></tr>
          <tr><td>Uniform &amp; Books</td><td colspan="2" style="text-align:center">UGX 200,000 per year</td></tr>
          <tr><td>Activity Fee</td><td>UGX 30,000</td><td>UGX 30,000</td></tr>
        </tbody>
      </table>
    </div>
    <div class="alert alert-info" style="margin-top:16px">Fees are payable at the beginning of each term. Payment plans are available — contact the bursar for details.</div>
  </div>

  <!-- APPLY CTA -->
  <div class="card" style="text-align:center;background:linear-gradient(135deg,var(--primary-dark),var(--primary));color:var(--white)">
    <h2 style="font-family:'Poppins',sans-serif;font-size:24px;font-weight:700;color:var(--white);margin-bottom:10px">Ready to Apply?</h2>
    <p style="color:rgba(255,255,255,0.8);margin-bottom:24px;font-size:15px">Fill in our online application form and our admissions team will contact you within 3–5 working days.</p>
    <a href="apply.php" class="btn btn-accent btn-lg">Apply Online Now</a>
  </div>

</div>

<?php include 'footer.php'; ?>
</body>
</html>
