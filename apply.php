<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STARLIGHT HIGH SCHOOL | APPLY FOR ADMISSION</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; background-color: #F9FAFB; color: #1F2937; line-height: 1.6; }

        /* HEADER */
        header { background-color: #2563EB; position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        nav { max-width: 1200px; margin: auto; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: flex; align-items: center; gap: 12px; color: white; }
        .brand img { width: 65px; }
        .brand-name { font-size: 18px; font-weight: bold; letter-spacing: 1px; }
        .brand p { font-size: 12px; opacity: 0.9; }
        nav ul { list-style: none; display: flex; gap: 25px; }
        nav ul li { position: relative; }
        nav ul li a { color: white; text-decoration: none; font-size: 14px; font-weight: bold; position: relative; }
        nav ul li a::after { content: ""; position: absolute; bottom: -6px; left: 0; width: 0%; height: 2px; background-color: #FCD34D; transition: width 0.3s ease; }
        nav ul li a:hover::after { width: 100%; }
        nav ul li a:hover { color: #FCD34D; }
        nav ul li ul { display: none; position: absolute; background-color: white; top: 100%; left: 0; min-width: 180px; border-radius: 6px; box-shadow: 0 8px 20px rgba(0,0,0,0.15); padding: 10px 0; }
        nav ul li:hover ul { display: block; }
        nav ul li ul li a { color: #1F2937; padding: 10px 20px; display: block; }
        nav ul li ul li a:hover { background-color: #F9FAFB; color: #2563EB; }

        /* MAIN */
        main { max-width: 860px; margin: 40px auto; padding: 0 20px; }

        /* SECTION CARDS */
        section { background: white; border-radius: 14px; padding: 35px; margin-bottom: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.07); }
        section h2 { color: #2563EB; font-size: 20px; margin-bottom: 24px; position: relative; }
        section h2::after { content: ""; width: 50px; height: 4px; background-color: #FF6B6B; display: block; margin-top: 8px; border-radius: 2px; }

        /* ERROR / SUCCESS MESSAGES */
        .error-msg { background-color: #FEF2F2; border-left: 4px solid #EF4444; color: #B91C1C; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
        .success-msg { background-color: #F0FDF4; border-left: 4px solid #16A34A; color: #15803D; padding: 16px 20px; border-radius: 8px; font-size: 15px; margin-bottom: 20px; }

        /* FORM */
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; font-size: 14px; margin-bottom: 6px; color: #374151; }
        input[type="text"], input[type="email"], input[type="tel"], input[type="date"],
        select, textarea {
            width: 100%; padding: 11px 14px; border: 1px solid #D1D5DB;
            border-radius: 8px; font-size: 14px; background-color: #F9FAFB;
            transition: border 0.3s ease, box-shadow 0.3s ease; font-family: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: #2563EB;
            background-color: white; box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .optional { font-weight: normal; font-size: 12px; color: #9CA3AF; margin-left: 4px; }
        .note { font-size: 13px; color: #6B7280; margin-top: 6px; }

        /* TWO COLUMN */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        /* SUBMIT */
        .submit-btn { width: 100%; padding: 14px; background-color: #2563EB; color: white; border: none; border-radius: 30px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s ease, transform 0.2s ease; margin-top: 10px; }
        .submit-btn:hover { background-color: #1D4ED8; transform: translateY(-2px); }

        /* FOOTER */
        footer { background-color: #1F2937; color: white; text-align: center; padding: 30px 20px; margin-top: 60px; }
        footer p { font-size: 14px; line-height: 1.8; opacity: 0.9; }

        @media (max-width: 600px) { .two-col { grid-template-columns: 1fr; } section { padding: 22px; } }
    </style>
</head>
<body>

<?php
$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'config.php';

    // Collect and sanitize all fields
    $first_name       = trim($_POST['first_name']       ?? '');
    $last_name        = trim($_POST['last_name']        ?? '');
    $date_of_birth    = trim($_POST['date_of_birth']    ?? '');
    $gender           = trim($_POST['gender']           ?? '');
    $nationality      = trim($_POST['nationality']      ?? '');
    $class_applied    = trim($_POST['class_applied']    ?? '');
    $student_type     = trim($_POST['student_type']     ?? '');
    $previous_school  = trim($_POST['previous_school']  ?? '');
    $ple_index        = trim($_POST['ple_index']        ?? '');
    $seniorfour_index = trim($_POST['seniorfour_index'] ?? '');
    $combination_type = trim($_POST['combination_type'] ?? '');
    $principal_one    = trim($_POST['principal_one']    ?? '');
    $principal_two    = trim($_POST['principal_two']    ?? '');
    $subsidiary       = trim($_POST['subsidiary']       ?? '');
    $combination_name = trim($_POST['combination_name'] ?? '');
    $guardian_name    = trim($_POST['guardian_name']    ?? '');
    $relationship     = trim($_POST['relationship']     ?? '');
    $guardian_phone   = trim($_POST['guardian_phone']   ?? '');
    $guardian_email   = trim($_POST['guardian_email']   ?? '');
    $address          = trim($_POST['address']          ?? '');

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($date_of_birth) ||
        empty($gender) || empty($class_applied) || empty($student_type) ||
        empty($guardian_name) || empty($guardian_phone)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Build combination string for storage
        $subjects = '';
        if (!empty($principal_one) || !empty($principal_two)) {
            $parts = array_filter([$principal_one, $principal_two, $subsidiary]);
            $subjects = implode(', ', $parts);
            if (!empty($combination_name)) $subjects .= ' (' . $combination_name . ')';
        }

        // Save to database using prepared statement
        $stmt = mysqli_prepare($conn,
            "INSERT INTO applications
            (first_name, last_name, date_of_birth, gender, nationality,
             class_applied, student_type, previous_school, ple_index,
             combination_type, combination_name, guardian_name,
             guardian_phone, guardian_email, address, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
        );

        mysqli_stmt_bind_param($stmt, 'ssssssssssssss',
            $first_name, $last_name, $date_of_birth, $gender, $nationality,
            $class_applied, $student_type, $previous_school, $ple_index,
            $combination_type, $subjects, $guardian_name,
            $guardian_phone, $guardian_email
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
        } else {
            $error = 'Something went wrong. Please try again.';
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<header>
    <nav>
        <div class="brand">
            <img src="images/logo.png" alt="logo">
            <div>
                <span class="brand-name">STARLIGHT HIGH SCHOOL</span>
                <p><em>Knowledge for Life and Service</em></p>
            </div>
        </div>
        <ul>
            <li><a href="home.html">HOME</a></li>
            <li><a href="about.html">ABOUT</a></li>
            <li><a href="academics.html">ACADEMICS</a></li>
            <li>
                <a href="admissions.html">ADMISSIONS</a>
                <ul>
                    <li><a href="admissions.html#requirements">Requirements</a></li>
                    <li><a href="apply.php">Apply Online</a></li>
                    <li><a href="admissions.html#fees">School Fees</a></li>
                </ul>
            </li>
            <li><a href="news.html">NEWS AND EVENTS</a></li>
            <li><a href="contact.html">CONTACT</a></li>
            <li><a href="login.php">LOGIN</a></li>
        </ul>
    </nav>
</header>

<main>

<?php if ($success): ?>
    <!-- SUCCESS STATE -->
    <section>
        <h2>Application Submitted</h2>
        <div class="success-msg">
            Your application has been submitted successfully! The school administration will review it and contact you via the phone number or email you provided.
        </div>
        <p style="margin-bottom:16px">Application details:</p>
        <p><strong>Name:</strong> <?= htmlspecialchars($first_name . ' ' . $last_name) ?></p>
        <p><strong>Class Applied:</strong> <?= htmlspecialchars($class_applied) ?></p>
        <p><strong>Guardian Contact:</strong> <?= htmlspecialchars($guardian_phone) ?></p>
        <br>
        <a href="home.html" style="display:inline-block;padding:12px 28px;background:#2563EB;color:white;border-radius:30px;text-decoration:none;font-weight:bold;">Back to Home</a>
    </section>

<?php else: ?>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="apply.php" method="POST">

        <!-- SECTION 1: PERSONAL INFO -->
        <section>
            <h2>Student Personal Information</h2>

            <div class="two-col">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="Enter first name"
                           value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="Enter last name"
                           value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth"
                           value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">-- Select Gender --</option>
                        <option value="Male"   <?= (($_POST['gender'] ?? '') === 'Male')   ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Nationality</label>
                    <input type="text" name="nationality" placeholder="e.g. Ugandan"
                           value="<?= htmlspecialchars($_POST['nationality'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Student Type</label>
                    <select name="student_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="Day"      <?= (($_POST['student_type'] ?? '') === 'Day')      ? 'selected' : '' ?>>Day Student</option>
                        <option value="Boarding" <?= (($_POST['student_type'] ?? '') === 'Boarding') ? 'selected' : '' ?>>Boarding Student</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Class Applying For</label>
                <select name="class_applied" required>
                    <option value="">-- Select Class --</option>
                    <?php
                    $classes = ['S1'=>'Senior One (S1)','S2'=>'Senior Two (S2)','S3'=>'Senior Three (S3)',
                                'S4'=>'Senior Four (S4)','S5'=>'Senior Five (S5)','S6'=>'Senior Six (S6)'];
                    foreach ($classes as $val => $label) {
                        $sel = (($_POST['class_applied'] ?? '') === $val) ? 'selected' : '';
                        echo "<option value=\"$val\" $sel>$label</option>";
                    }
                    ?>
                </select>
            </div>
        </section>

        <!-- SECTION 2: PREVIOUS SCHOOL -->
        <section>
            <h2>Previous School Information</h2>

            <div class="form-group">
                <label>Previous School Name</label>
                <input type="text" name="previous_school" placeholder="Name of previous school"
                       value="<?= htmlspecialchars($_POST['previous_school'] ?? '') ?>" required>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>PLE Results <span class="optional">(if applicable)</span></label>
                    <input type="text" name="ple_index" placeholder="e.g. U/2024/001234"
                           value="<?= htmlspecialchars($_POST['ple_index'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Senior Four Index <span class="optional">(if applicable)</span></label>
                    <input type="text" name="seniorfour_index" placeholder="e.g. U2024/004/001"
                           value="<?= htmlspecialchars($_POST['seniorfour_index'] ?? '') ?>">
                </div>
            </div>
        </section>

        <!-- SECTION 3: A-LEVEL COMBINATION -->
        <section>
            <h2>A-Level Subject Combination <span class="optional">(S5 & S6 applicants only)</span></h2>
            <p class="note" style="margin-bottom:20px">Leave blank if applying for S1–S4.</p>

            <div class="two-col">
                <div class="form-group">
                    <label>Combination Type</label>
                    <select name="combination_type">
                        <option value="">-- Select --</option>
                        <option value="Sciences" <?= (($_POST['combination_type'] ?? '') === 'Sciences') ? 'selected' : '' ?>>Sciences</option>
                        <option value="Arts"     <?= (($_POST['combination_type'] ?? '') === 'Arts')     ? 'selected' : '' ?>>Arts</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Combination Name <span class="optional">(if known)</span></label>
                    <input type="text" name="combination_name" placeholder="e.g. PCM, HEG, MEG"
                           value="<?= htmlspecialchars($_POST['combination_name'] ?? '') ?>">
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Principal Subject 1</label>
                    <select name="principal_one">
                        <option value="">-- Select Subject --</option>
                        <?php
                        $subjects_list = ['Mathematics','Physics','Chemistry','Biology','Economics','Geography','History','Literature','Divinity'];
                        foreach ($subjects_list as $s) {
                            $sel = (($_POST['principal_one'] ?? '') === $s) ? 'selected' : '';
                            echo "<option value=\"$s\" $sel>$s</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Principal Subject 2</label>
                    <select name="principal_two">
                        <option value="">-- Select Subject --</option>
                        <?php
                        foreach ($subjects_list as $s) {
                            $sel = (($_POST['principal_two'] ?? '') === $s) ? 'selected' : '';
                            echo "<option value=\"$s\" $sel>$s</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Subsidiary Subject</label>
                <select name="subsidiary">
                    <option value="">-- Select Subject --</option>
                    <?php
                    $subs = ['General Paper','ICT','Entrepreneurship','Sub Mathematics'];
                    foreach ($subs as $s) {
                        $sel = (($_POST['subsidiary'] ?? '') === $s) ? 'selected' : '';
                        echo "<option value=\"$s\" $sel>$s</option>";
                    }
                    ?>
                </select>
            </div>
        </section>

        <!-- SECTION 4: PARENT / GUARDIAN -->
        <section>
            <h2>Parent / Guardian Information</h2>

            <div class="two-col">
                <div class="form-group">
                    <label>Guardian Full Name</label>
                    <input type="text" name="guardian_name" placeholder="Full name"
                           value="<?= htmlspecialchars($_POST['guardian_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Relationship to Student</label>
                    <input type="text" name="relationship" placeholder="e.g. Father, Mother, Uncle"
                           value="<?= htmlspecialchars($_POST['relationship'] ?? '') ?>" required>
                </div>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="guardian_phone" placeholder="+256 700 000 000"
                           value="<?= htmlspecialchars($_POST['guardian_phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address <span class="optional">(optional)</span></label>
                    <input type="email" name="guardian_email" placeholder="guardian@email.com"
                           value="<?= htmlspecialchars($_POST['guardian_email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Home Address</label>
                <textarea name="address" rows="3" placeholder="Village, Sub-county, District"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Application</button>
        </section>

    </form>

<?php endif; ?>

</main>

<footer>
    <p><strong>Starlight High School</strong><br>
        Private Mixed Secondary School | UNEB Curriculum<br>
        Email: info@starlighthigh.ac.ug | Phone: +256 700 567 891<br>
        &copy; 2026 Starlight High School. All rights reserved.
    </p>
</footer>

</body>
</html>
