<?php
// Include PHPMailer library files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = '';
$message_class = '';
$admin_email = '11chrisjones.04@gmail.com';
$admin_name = 'Admin';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_type = $_POST['appointment_type'];
    $fasting_hours = $_POST['fasting_hours'];
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_type, fasting_hours, date, time_slot, name, age, address, contact_number, email, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Booked')");
    $stmt->bind_param("isssssssss", $_SESSION['user_id'], $appointment_type, $fasting_hours, $date, $time_slot, $name, $age, $address, $contact_number, $email);

    if ($stmt->execute()) {
        // Send email notification using PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jnabubos@gmail.com';
            $mail->Password   = 'alxhljezdphvawic';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('jnabubos@gmail.com', 'Medicalista: JEDA Online Appointment and Information Management System');
            $mail->addAddress($email, $name);       // User's email
            $mail->addAddress($admin_email, $admin_name); // Admin's email

            // Content for User
            $mail->isHTML(true);
            $mail->Subject = 'Appointment Confirmation';
            $mail->Body    = "Dear $name,<br><br>Your appointment for $appointment_type on $date at $time_slot has been successfully booked.<br><br>Thank you,<br>Medical Booking Website";
            $mail->send();

            // Content for Admin
            $mail->clearAddresses();
            $mail->addAddress($admin_email, $admin_name);
            $mail->Subject = 'New Appointment Booked';
            $mail->Body    = "Dear $admin_name,<br><br>A new appointment has been booked:<br>
                              <b>Appointment Type:</b> $appointment_type<br>
                              <b>Date:</b> $date<br>
                              <b>Time Slot:</b> $time_slot<br>
                              <b>Name:</b> $name<br>
                              <b>Age:</b> $age<br>
                              <b>Address:</b> $address<br>
                              <b>Contact Number:</b> $contact_number<br>
                              <b>Email:</b> $email<br><br>
                              Thank you,<br>Medical Booking Website";
            $mail->send();

            $message = "Appointment booked successfully! A confirmation email has been sent.";
            $message_class = "success";
        } catch (Exception $e) {
            $message = "Appointment booked, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $message_class = "error";
        }
    } else {
        $message = "Error booking appointment: " . $stmt->error;
        $message_class = "error";
    }

    $stmt->close();
}