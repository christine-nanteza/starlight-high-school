<?php include 'nav.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>STARLIGHT HIGH SCHOOL | CONTACT</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'nav.php'; ?>

<div style="background:linear-gradient(135deg,var(--primary-dark),var(--primary));padding:50px 24px;text-align:center">
  <div style="max-width:700px;margin:0 auto">
    <span class="hero-badge">Contact Us</span>
    <h1 style="font-family:'Poppins',sans-serif;font-size:36px;font-weight:700;color:var(--white);margin-top:14px">Get In Touch</h1>
    <p style="color:rgba(255,255,255,0.8);margin-top:12px">We'd love to hear from you. Reach out to us through any of the channels below.</p>
  </div>
</div>

<div class="page-wrapper-md">
  <div style="display:grid;grid-template-columns:1fr 1.6fr;gap:24px;align-items:start">

    <!-- CONTACT INFO -->
    <div>
      <div class="card" style="margin-bottom:0">
        <h2 class="card-title">Contact Information</h2>
        <div style="height:16px"></div>
        <?php foreach([
          ['📍','Address','Kyengera, Kampala, Uganda'],
          ['📞','Phone','+256 700 567 891'],
          ['✉️','Email','info@starlighthigh.ac.ug'],
          ['🕐','Office Hours','Mon–Fri: 8:00 AM – 5:00 PM'],
        ] as [$icon,$label,$value]): ?>
        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:18px">
          <div style="width:42px;height:42px;background:var(--bg);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><?=$icon?></div>
          <div>
            <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px"><?=$label?></div>
            <div style="font-size:15px;font-weight:600;color:var(--primary)"><?=$value?></div>
          </div>
        </div>
        <?php endforeach; ?>

        <!-- MAP -->
        <div style="border-radius:var(--radius-md);overflow:hidden;margin-top:10px;border:2px solid var(--border)">
          <iframe src="https://www.google.com/maps?q=Kyengera+Kampala+Uganda&output=embed" width="100%" height="220" style="border:none;display:block" loading="lazy"></iframe>
        </div>
      </div>
    </div>

    <!-- CONTACT FORM -->
    <div class="card" style="margin-bottom:0">
      <h2 class="card-title">Send Us a Message</h2>
      <div style="height:16px"></div>
      <form>
        <div class="form-grid-2">
          <div class="form-group">
            <label class="form-label">Your Name</label>
            <input type="text" class="form-control" placeholder="Full name" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" placeholder="your@email.com" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Message Type</label>
          <select class="form-control">
            <option value="">-- Select Type --</option>
            <option>General Inquiry</option>
            <option>Admission Inquiry</option>
            <option>Complaint</option>
            <option>Feedback</option>
            <option>Other</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Message</label>
          <textarea class="form-control" rows="5" placeholder="Type your message here..."></textarea>
        </div>
        <div class="btn-row">
          <button type="submit" class="btn btn-primary btn-full">Send Message</button>
          <button type="reset" class="btn btn-gray">Clear</button>
        </div>
      </form>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
