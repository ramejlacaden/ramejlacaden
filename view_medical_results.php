<?php
session_start();
include('config.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit;
}

// Fetch all medical results along with appointment details
$sql = "SELECT m.*, a.name, a.date, a.time_slot FROM medical_results m
        JOIN appointment_archive a ON m.appointment_id = a.id
        ORDER BY a.date DESC";

$results = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="view_medical_results.php">
            <img src="jeda.png" alt="Logo" style="height: 50px;"> <!-- Logo -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="medical_results.php">Back to Medical Results</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Medical Results</h1>

    <?php if ($results->num_rows > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient Name</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Diagnosis</th>
                    <th>Prescription</th>
                    <th>Additional Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                        <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>
                        <td><?php echo htmlspecialchars($row['prescription']); ?></td>
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        <td>
                            <a href="medical_results.php?appointment_id=<?php echo $row['appointment_id']; ?>" class="btn btn-primary">Edit</a>
                            <button class="btn btn-secondary" onclick="window.print();">Print</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center text-danger">No medical results found.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
