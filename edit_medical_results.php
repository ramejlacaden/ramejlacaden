<?php
session_start();
include('config.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit;
}

// Get the appointment_id from the URL
if (!isset($_GET['appointment_id']) || !is_numeric($_GET['appointment_id'])) {
    die("Invalid appointment ID.");
}

$appointment_id = $_GET['appointment_id'];

// Fetch the medical results from the database
$stmt = $conn->prepare("SELECT * FROM medical_results WHERE appointment_id = ?");
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$medical_result = $stmt->get_result()->fetch_assoc();

// Handle form submission to update medical results
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diagnosis = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];
    $notes = $_POST['notes'];

    // Update the medical results in the database
    $stmt = $conn->prepare("UPDATE medical_results SET diagnosis = ?, prescription = ?, notes = ? WHERE appointment_id = ?");
    $stmt->bind_param("sssi", $diagnosis, $prescription, $notes, $appointment_id);
    $stmt->execute();
    $message = "Medical results updated successfully!";
    
    // Redirect back to the view page
    header("Location: view_medical_results.php?appointment_id=" . $appointment_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medical Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Medical Results for Appointment #<?php echo htmlspecialchars($appointment_id); ?></h1>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="diagnosis" class="form-label">Diagnosis</label>
            <textarea id="diagnosis" name="diagnosis" class="form-control" required><?php echo htmlspecialchars($medical_result['diagnosis']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="prescription" class="form-label">Prescription</label>
            <textarea id="prescription" name="prescription" class="form-control" required><?php echo htmlspecialchars($medical_result['prescription']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Additional Notes</label>
            <textarea id="notes" name="notes" class="form-control"><?php echo htmlspecialchars($medical_result['notes']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Update Medical Results</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
