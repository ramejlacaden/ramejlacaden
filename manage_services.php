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

// Handle form submission to update service price
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['service_id'])) {
    $service_id = $_POST['service_id'];
    $price = $_POST['price'];
    $service_name = $_POST['service_name'];
    $category = $_POST['category'];

    // Update service using prepared statements
    $stmt = $conn->prepare("UPDATE services SET service_name=?, category=?, price=? WHERE id=?");
    $stmt->bind_param("ssdi", $service_name, $category, $price, $service_id);

    if ($stmt->execute()) {
        $message = "Service updated successfully!";
        $message_class = "alert-success";
    } else {
        $message = "Error updating service: " . $stmt->error;
        $message_class = "alert-danger";
    }

    $stmt->close();
}

// Handle service deletion
if (isset($_GET['delete_service_id'])) {
    $delete_service_id = $_GET['delete_service_id'];
    
    // Delete service
    $stmt = $conn->prepare("DELETE FROM services WHERE id=?");
    $stmt->bind_param("i", $delete_service_id);

    if ($stmt->execute()) {
        $message = "Service deleted successfully!";
        $message_class = "alert-success";
    } else {
        $message = "Error deleting service: " . $stmt->error;
        $message_class = "alert-danger";
    }

    $stmt->close();
}

// Fetch all services
$services = mysqli_query($conn, "SELECT * FROM services");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #dff5e7;
        }

        .container {
            margin-top: 50px;
        }

        .table {
            margin-top: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        .table th {
            background-color: #eaf4f0;
            font-weight: bold;
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
                    <a class="nav-link" href="admin.php">Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_service.php">Add New Service</a> <!-- Link to Add New Service page -->
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

    <!-- Manage Services -->
    <h2>Manage Services</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Service ID</th>
                    <th>Service Name</th>
                    <th>Category</th>
                    <th>Price (PHP)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($services)) { ?>
                    <tr>
                        <form method="POST" action="">
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td>
                                <input type="text" name="service_name" class="form-control" value="<?php echo htmlspecialchars($row['service_name']); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($row['category']); ?>" required>
                            </td>
                            <td>
                                <input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($row['price']); ?>" step="0.01" required>
                                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            </td>
                            <td>
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="manage_services.php?delete_service_id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
