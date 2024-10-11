<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = $_POST['username']; // This will now be used for both email and username
    $password = $_POST['password'];

    // Update the SQL query to check for either username or email
    $sql = "SELECT id, password FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $login_input, $login_input); // Bind the same variable for both parameters
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        header("location: book_appointment.php");
        exit(); // Add exit to prevent further script execution
    } else {
        $error_message = "Invalid username or password";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #d1f2d1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-form {
            background-color: #f0f9f0;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .logo {
            height: 60px;
            margin-bottom: 20px;
        }

        .input-group {
            position: relative;
            margin-bottom: 10px;
        }

        input.form-control {
            border-radius: 50px;
            padding: 10px 20px;
        }

        .toggle-password {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
        }

        .btn-custom {
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 20px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
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

        .forgot-password,
        .create-account {
            display: block;
            margin: 15px 0;
            text-decoration: none;
            font-size: 13px;
            color: #28a745;
        }

        .forgot-password:hover,
        .create-account:hover {
            text-decoration: underline;
        }

        .error {
            color: #ff0000;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form mx-auto">
            <img src="jeda.png" alt="Logo" class="logo">
            <h2 class="mb-4">Log Into Your Account</h2>
            <?php if (isset($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="input-group">
                    <input type="text" name="username" class="form-control" placeholder="Your Email or Phone Here" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Your Password Here" required>
                    <span class="toggle-password" onclick="togglePassword()">🙈</span>
                </div>
                <button type="submit" class="btn btn-primary btn-custom w-100 mt-3">Log In</button>
            </form>
            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
            <div class="divider">or</div>
            <a href="register.php" class="btn btn-success btn-custom w-100">Create new account</a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '👁️';
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '🙈';
            }
        }
    </script>
</body>
</html>
