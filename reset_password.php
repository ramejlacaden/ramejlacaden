<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $new_password = $_POST['new_password'];

    if ($otp == $_SESSION['otp']) {
        $email = $_SESSION['email'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed_password, $email);

        if ($stmt->execute()) {
            // Retrieve user ID for session initialization after password reset
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                // Set session variables and redirect to the login page
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                header("Location: login.php");
                exit();
            } else {
                $error_message = "User not found.";
            }
        } else {
            $error_message = "Error resetting password.";
        }
        $stmt->close();
    } else {
        $error_message = "Invalid OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #d1f2d1;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .reset-password-form {
            background-color: #f0f9f0;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 450px;
            text-align: center;
        }
        .reset-password-form input[type="text"],
        .reset-password-form input[type="password"] {
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 16px;
            width: 100%;
            margin-bottom: 15px;
        }
        .reset-password-form .btn {
            border-radius: 50px;
            padding: 10px;
            width: 100%;
            font-size: 16px;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="reset-password-form">
        <h2 class="mb-4">Reset Password</h2>
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="otp" placeholder="Enter OTP" required><br>
            <input type="password" name="new_password" placeholder="Enter New Password" required><br>
            <button type="submit" class="btn btn-success">Reset Password</button>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
