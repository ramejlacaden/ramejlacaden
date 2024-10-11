<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['admin_id'] = $row['id'];
        header("Location: admin.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
        input[type="submit"], .create-account-button {
            width: 100%;
            padding: 15px;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            box-sizing: border-box;
            font-size: 16px;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }
        input[type="submit"] {
            background-color: #007BFF;
            margin-bottom: 10px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .or-text {
            margin: 15px 0;
            font-size: 14px;
            color: #888;
        }
        .create-account-button {
            background-color: #28a745; /* Mint green */
        }
        .create-account-button:hover {
            background-color: #218838; /* Darker mint green */
        }
        .forgot-password {
            margin-top: 10px;
            font-size: 14px;
        }
        .forgot-password a {
            color: #007BFF;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .create-account {
            margin-top: 10px;
            font-size: 14px;
        }
        .create-account-button {
            color: white;
            text-decoration: none;
        }
        .create-account-button:hover {
            text-decoration: none;
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
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Your Email or Phone Here" required>
            <input type="password" name="password" placeholder="Your Password Here" required>
            <input type="submit" value="Log In">
        </form>
        <div class="forgot-password">
            <a href="#">Forgot Password?</a>
        </div>
        <div class="or-text">or</div>
        <div class="create-account">
            <a href="admin_register.php" class="create-account-button">Create new account</a>
        </div>
    </div>
</body>
</html>
