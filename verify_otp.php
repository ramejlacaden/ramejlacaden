<?php
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];

    // Check if the OTP matches
    if ($otp == $_SESSION['otp']) {
        // OTP is correct, insert user data into the database
        include('config.php');

        $username = $_SESSION['username'];
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $password);
            if ($stmt->execute()) {
                // Registration successful, clear session data
                unset($_SESSION['otp']);
                unset($_SESSION['username']);
                unset($_SESSION['email']);
                unset($_SESSION['password']);

                // Redirect to login page or dashboard
                header("location: login.php");
                exit;
            } else {
                $error_message = "Error inserting data: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing statement: " . $conn->error;
        }
    } else {
        $error_message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
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
            max-width: 100px; /* Adjusted logo size */
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 22px; /* Slightly smaller font size */
            color: #333;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 8px; /* Adjusted padding for smaller inputs */
            margin: 10px 0;
            border-radius: 25px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-size: 14px; /* Adjusted font size */
        }
        input[type="submit"] {
            background-color: #28a745; /* Green button */
            color: white;
            border: none;
            cursor: pointer;
        }
        .register-button {
            background-color: #007bff; /* Blue button */
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            padding: 10px;
            border-radius: 25px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .register-button:hover {
            background-color: #0056b3;
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
            <h2>Email Verification</h2>
            <?php if (!empty($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="verify_otp.php">
                <div class="input-group">
                    <input type="text" name="otp" placeholder="Enter OTP" required>
                </div>
                <div class="input-group">
                    <input type="submit" value="Verify">
                </div>
            </form>
            <a href="register.php" class="register-button">Register</a>
        </div>
    </div>
</body>
</html>
