<?php
// ============================================
// STARLIGHT HIGH SCHOOL — EMAIL HELPER
// ============================================
// Uses PHP's built-in mail() function.
// For reliable delivery on a live server,
// replace with PHPMailer + SMTP (Gmail/SendGrid).
// ============================================

function send_email($to, $to_name, $subject, $body_html) {
    $from_name  = 'Starlight High School';
    $from_email = 'noreply@starlighthigh.ac.ug';

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $from_name <$from_email>\r\n";
    $headers .= "Reply-To: info@starlighthigh.ac.ug\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    $full_body = email_template($to_name, $body_html);

    return mail($to, $subject, $full_body, $headers);
}

function email_template($name, $content) {
    return "
    <!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'></head>
    <body style='margin:0;padding:0;background:#f4f7fb;font-family:Arial,sans-serif;'>
      <div style='max-width:600px;margin:30px auto;background:white;border-radius:12px;overflow:hidden;box-shadow:0 4px 15px rgba(0,0,0,0.08)'>

        <!-- Header -->
        <div style='background:#2563EB;padding:28px 30px;text-align:center'>
          <h1 style='color:white;margin:0;font-size:22px;letter-spacing:1px'>STARLIGHT HIGH SCHOOL</h1>
          <p style='color:#bfdbfe;margin:5px 0 0;font-size:13px'>Knowledge for Life and Service</p>
        </div>

        <!-- Body -->
        <div style='padding:30px'>
          <p style='color:#374151;font-size:15px;margin-bottom:20px'>Dear <strong>$name</strong>,</p>
          $content
          <hr style='border:none;border-top:1px solid #e5e7eb;margin:28px 0'>
          <p style='color:#9ca3af;font-size:12px;text-align:center'>
            Starlight High School | Kyengera, Kampala<br>
            Phone: +256 700 567 891 | Email: info@starlighthigh.ac.ug<br>
            <em>This is an automated message. Please do not reply directly to this email.</em>
          </p>
        </div>

      </div>
    </body>
    </html>";
}

// ============================================
// NOTIFICATION FUNCTIONS
// ============================================

// 1. Application received (to guardian)
function notify_application_received($guardian_email, $guardian_name, $student_name, $class_applied) {
    $subject = "Application Received — $student_name | Starlight High School";
    $body    = "
    <p style='color:#374151;font-size:15px'>Thank you for submitting an admission application for <strong>$student_name</strong> to join <strong>$class_applied</strong>.</p>
    <div style='background:#EFF6FF;border-left:4px solid #2563EB;padding:16px;border-radius:8px;margin:20px 0'>
        <p style='color:#1D4ED8;margin:0;font-size:14px'><strong>What happens next?</strong><br>
        Our admissions team will review your application and contact you within <strong>3–5 working days</strong>.</p>
    </div>
    <p style='color:#374151;font-size:14px'>If you have any questions, contact us at <a href='mailto:info@starlighthigh.ac.ug' style='color:#2563EB'>info@starlighthigh.ac.ug</a> or call <strong>+256 700 567 891</strong>.</p>
    ";
    return send_email($guardian_email, $guardian_name, $subject, $body);
}

// 2. Application approved (to guardian)
function notify_application_approved($guardian_email, $guardian_name, $student_name, $class_applied) {
    $subject = "Application Approved — $student_name | Starlight High School";
    $body    = "
    <p style='color:#374151;font-size:15px'>We are pleased to inform you that the admission application for <strong>$student_name</strong> has been <strong style='color:#16a34a'>approved</strong>.</p>
    <div style='background:#F0FDF4;border-left:4px solid #16A34A;padding:16px;border-radius:8px;margin:20px 0'>
        <p style='color:#166534;margin:0;font-size:14px'><strong>Class:</strong> $class_applied<br>
        <strong>Next Step:</strong> Please visit the school to complete enrollment and pay the first term fees.</p>
    </div>
    <p style='color:#374151;font-size:14px'>Welcome to the Starlight High School family! Contact us at <a href='mailto:info@starlighthigh.ac.ug' style='color:#2563EB'>info@starlighthigh.ac.ug</a> for more details.</p>
    ";
    return send_email($guardian_email, $guardian_name, $subject, $body);
}

// 3. Application rejected (to guardian)
function notify_application_rejected($guardian_email, $guardian_name, $student_name) {
    $subject = "Application Update — $student_name | Starlight High School";
    $body    = "
    <p style='color:#374151;font-size:15px'>Thank you for your interest in Starlight High School. After careful consideration, we regret to inform you that the admission application for <strong>$student_name</strong> was not successful at this time.</p>
    <p style='color:#374151;font-size:14px'>We encourage you to apply again in the next intake. For more information, please contact us at <a href='mailto:info@starlighthigh.ac.ug' style='color:#2563EB'>info@starlighthigh.ac.ug</a> or call <strong>+256 700 567 891</strong>.</p>
    ";
    return send_email($guardian_email, $guardian_name, $subject, $body);
}

// 4. New student account created (to student/guardian)
function notify_account_created($student_email, $student_name, $reg_number, $class) {
    $login_url = 'https://starlighthigh.ac.ug/login.php';
    $subject   = "Your Starlight Portal Account | $student_name";
    $body      = "
    <p style='color:#374151;font-size:15px'>A student portal account has been created for <strong>$student_name</strong>.</p>
    <div style='background:#EFF6FF;border-left:4px solid #2563EB;padding:16px;border-radius:8px;margin:20px 0'>
        <p style='color:#1D4ED8;margin:0;font-size:14px'>
            <strong>Login Details:</strong><br>
            Email: <strong>$student_email</strong><br>
            Password: <strong>$reg_number</strong> (your registration number)<br>
            Class: <strong>$class</strong>
        </p>
    </div>
    <p style='color:#dc2626;font-size:13px'><strong>Important:</strong> Please change your password after your first login.</p>
    <p style='text-align:center;margin-top:20px'>
        <a href='$login_url' style='display:inline-block;padding:12px 28px;background:#2563EB;color:white;border-radius:8px;text-decoration:none;font-weight:bold;font-size:14px'>Login to Your Portal</a>
    </p>
    ";
    return send_email($student_email, $student_name, $subject, $body);
}

// 5. Marks approved (to student)
function notify_marks_approved($student_email, $student_name, $term, $year) {
    $subject = "Your Results Are Ready — $term $year | Starlight High School";
    $body    = "
    <p style='color:#374151;font-size:15px'>Your results for <strong>$term $year</strong> have been approved and are now available on your student portal.</p>
    <p style='text-align:center;margin-top:20px'>
        <a href='https://starlighthigh.ac.ug/report.php' style='display:inline-block;padding:12px 28px;background:#2563EB;color:white;border-radius:8px;text-decoration:none;font-weight:bold;font-size:14px'>View My Report Card</a>
    </p>
    ";
    return send_email($student_email, $student_name, $subject, $body);
}
?>
