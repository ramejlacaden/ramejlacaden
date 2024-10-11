<?php
session_start();
include('config.php');
require 'vendor/autoload.php'; // Ensure PHPMailer is properly included

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic form validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $error_message = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_message = "Username or email already taken. Please choose another one.";
            } else {
                // Generate OTP
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = password_hash($password, PASSWORD_BCRYPT);

                // Send OTP email
                $mail = new PHPMailer\PHPMailer\PHPMailer();
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'jnabubos@gmail.com'; // Your Gmail address
                    $mail->Password   = 'alxhljezdphvawic';    // Your Gmail app password
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    // Email content
                    $mail->setFrom('jnabubos@gmail.com', 'Medicalista: JEDA Online Appointment and Information Management System');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification OTP';
                    $mail->Body    = "Hello <strong>$username</strong>,<br><br>Your OTP for email verification is: <strong>$otp</strong><br><br>If you did not request this, please ignore this email.<br><br>Best regards,<br>Medicalista: JEDA Online Appointment and Information Management System";

                    // Attempt to send the email
                    if ($mail->send()) {
                        header("location: verify_otp.php");
                        exit;
                    } else {
                        throw new Exception("Mailer Error: " . $mail->ErrorInfo);
                    }
                } catch (Exception $e) {
                    $error_message = "Failed to send OTP. Please try again later.";
                    error_log($e->getMessage()); // Log the error for debugging purposes
                }
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
    <style>
        body {
            background-color: #98FF98; /* Mint green */
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #d1f2d1; /* Soft mint green background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #f0f9f0; /* Slightly off-white for contrast */
            padding: 30px; /* Adjusted padding for a smaller form */
            border-radius: 20px; /* Large radius for a rounded look */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15); /* Soft shadow */
            text-align: center;
            width: 350px; /* Adjusted width for a smaller form */
            position: relative;
        }
        .logo {
            max-width: 80px; /* Adjusted logo size */
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 22px; /* Slightly smaller font size */
            color: #333;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px; /* Adjusted padding for smaller inputs */
            margin: 10px 0;
            border-radius: 25px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-size: 14px; /* Adjusted font size */
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 20px 0;
            border-radius: 25px;
            background-color: #28a745; /* green button */
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .login-link {
            display: block;
            margin-top: 20px;
            text-decoration: none;
            color: white;
            background-color: #007bff; /* Blue button for back to login */
            padding: 10px;
            border-radius: 25px;
            transition: background-color 0.3s;
        }
        .login-link:hover {
            background-color: #218838; /* Darker green on hover */
        }
        .divider {
            margin: 20px 0;
            color: #888;
            position: relative;
            text-align: center;
        }
        .divider::before, .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background-color: #ddd;
        }
        .divider::before {
            left: 0;
        }
        .divider::after {
            right: 0;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <img src="jeda.jpg" alt="Logo" class="logo">
            <h2>Create an Account</h2>
            <?php if (!empty($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="register.php">
                <input type="text" name="username" placeholder="Username" value="" required><br>
                <input type="email" name="email" placeholder="Email" value="" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
                <input type="submit" value="Register">
            </form>

            <div class="divider">or</div>
            <a href="login.php" class="login-link">Log In</a>
        </div>
    </div>
</body>
</html>
