<?php
session_start();
include('config.php');

$message = ""; // Initialize the message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate that passwords match
    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_class = "error";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the username already exists
        $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username already exists.";
            $message_class = "error";
        } else {
            // Insert new user
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: admin_login.php");
                exit; // Ensure no further code is executed
            } else {
                $message = "Error: " . $stmt->error;
                $message_class = "error";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #d4f8e8; /* Soft mint green background */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 400px;
            padding: 30px;
            background-color: #f4f7f6;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 120px; /* Adjust according to your logo size */
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
            font-size: 16px;
            background-color: white;
        }
        input[type="submit"] {
            width: 100%;
            padding: 15px;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            box-sizing: border-box;
            font-size: 16px;
            background-color: #28a745; /* Mint green button */
        }
        input[type="submit"]:hover {
            background-color: #218838; /* Darker mint green */
        }
        .or-text {
            margin: 15px 0;
            font-size: 14px;
            color: #888;
        }
        .login-button {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            color: white;
            background-color: #007BFF; /* Blue button */
            border: none;
            border-radius: 25px;
            cursor: pointer;
            box-sizing: border-box;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .login-button:hover {
            background-color: #0056b3; /* Darker blue */
        }
        .error, .success {
            text-align: center;
            color: white;
            padding: 10px;
            margin-top: 10px;
            border-radius: 25px;
        }
        .error {
            background-color: #dc3545;
        }
        .success {
            background-color: #28a745;
        }
        hr {
            border: none;
            height: 1px;
            background-color: #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="jeda.png" alt="Logo"> <!-- Replace with your actual logo path -->
        </div>
        <h2>Admin Register</h2>
        <?php if (!empty($message)): ?>
            <div class="<?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" value="Register">
        </form>
        <div class="or-text">or</div>
        <div>
            <a href="admin_login.php" class="login-button">Log In</a>
        </div>
    </div>
</body>
</html>
