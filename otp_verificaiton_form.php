<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <img src="image.jpg" alt="Logo" class="logo">
            <h2>Verify OTP</h2>
            <?php if (!empty($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="POST" action="verify_otp.php">
                <input type="text" name="otp" placeholder="Enter OTP" required><br>
                <input type="submit" value="Verify">
            </form>
        </div>
    </div>
</body>
</html>
