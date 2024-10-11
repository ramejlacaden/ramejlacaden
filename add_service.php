<?php
session_start();
include('config.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit;
}

// Initialize message variables
$message = "";
$message_class = "";

// Handle form submission to add a new service
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_service_name = $_POST['new_service_name'];
    $category = $_POST['category'];
    $new_price = $_POST['new_price'];

    // Insert new service
    $stmt = $conn->prepare("INSERT INTO services (service_name, category, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $new_service_name, $category, $new_price);

    if ($stmt->execute()) {
        $message = "New service added successfully!";
        $message_class = "alert-success";
    } else {
        $message = "Error adding new service: " . $stmt->error;
        $message_class = "alert-danger";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dff5e7;
        }

        .container {
            margin-top: 50px;
        }

        .navbar-custom {
            background-color: #028482;
            padding: 10px;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white;
        }

        .navbar-custom .nav-link:hover {
            color: #b3ffdd;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }
    </style>
</head>

<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">
            <img src="jeda.png" alt="Logo"> <!-- The path to your logo (jeda.png) -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="manage_services.php">Manage Services</a> <!-- Link to Manage Services page -->
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <?php if (!empty($message)) : ?>
        <div class="alert <?php echo $message_class; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Add New Service -->
    <h2>Add New Service</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="new_service_name" class="form-label">Service Name</label>
            <input type="text" name="new_service_name" id="new_service_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <input type="text" name="category" id="category" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_price" class="form-label">Price (PHP)</label>
            <input type="number" name="new_price" id="new_price" class="form-control" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-success">Add Service</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
