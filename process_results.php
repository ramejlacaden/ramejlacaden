<?php
session_start();
include('config.php');
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Validate appointment_id from POST
if (!isset($_POST['appointment_id']) || !is_numeric($_POST['appointment_id'])) {
    echo "<div class='alert alert-danger'>Invalid or missing appointment ID.</div>";
    exit;
}

$appointment_id = intval($_POST['appointment_id']);

// Fetch appointment details from the database
$query = "SELECT * FROM appointment_archive WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();

if (!$appointment) {
    echo "<div class='alert alert-danger'>Appointment not found.</div>";
    exit;
}

// Helper function to retrieve POST data safely
function getPostData($key) {
    return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : 'N/A';
}

// Collect form data from POST
$category = getPostData('form_category');
$HBsAg = getPostData('HBsAg');
$HAV_IgM = getPostData('HAV_IgM');
$RPR_VDRL = getPostData('RPR_VDRL');
$HIV_Test = getPostData('HIV_Test');
$Blood_Type = getPostData('Blood_Type');
$Pregnancy_Test = getPostData('Pregnancy_Test');
$Others = getPostData('Others');
$Physician = getPostData('Physician');
$Age_Sex = getPostData('Age_Sex');
$Date = getPostData('Date');

// Send email with PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com'; // Replace with your Gmail address
    $mail->Password   = 'your_app_password'; // Replace with your Gmail App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Medical Clinic');
    $mail->addAddress($appointment['email'], $appointment['name']);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Medical Results for Your Appointment';
    $mail->Body    = '
        <h3>Medical Results for Appointment #' . htmlspecialchars($appointment_id) . '</h3>
        <p><strong>Category:</strong> ' . $category . '</p>
        <p><strong>Physician:</strong> ' . $Physician . '</p>
        <p><strong>Age/Sex:</strong> ' . $Age_Sex . '</p>
        <p><strong>Date:</strong> ' . $Date . '</p>
        <p><strong>HBsAg:</strong> ' . $HBsAg . '</p>
        <p><strong>HAV IgM:</strong> ' . $HAV_IgM . '</p>
        <p><strong>RPR/VDRL:</strong> ' . $RPR_VDRL . '</p>
        <p><strong>HIV Test:</strong> ' . $HIV_Test . '</p>
        <p><strong>Blood Type:</strong> ' . $Blood_Type . '</p>
        <p><strong>Pregnancy Test:</strong> ' . $Pregnancy_Test . '</p>
        <p><strong>Others:</strong> ' . $Others . '</p>
        <!-- Add other test results as needed -->
    ';

    $mail->send();
    echo "<div class='alert alert-success'>Medical results sent to the user successfully!</div>";
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Email could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
}
?>
