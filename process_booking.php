<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_id = $_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $time_slot = $_POST['time_slot'];
    $user_id = $_SESSION['user_id']; // Assuming the user is logged in

    // Insert the appointment into the database
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, service_id, appointment_date, time_slot) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $service_id, $appointment_date, $time_slot);

    if ($stmt->execute()) {
        echo "Appointment booked successfully!";
        // Redirect or show success message
    } else {
        echo "Error booking appointment: " . $stmt->error;
    }

    $stmt->close();
}
?>
