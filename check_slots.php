<?php
include('config.php');

if (isset($_GET['date'])) {
    $date = $_GET['date'];
    
    // Query to get booked time slots for the selected date
    $sql = "SELECT time_slot FROM appointments WHERE date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookedSlots = [];
    
    while ($row = $result->fetch_assoc()) {
        $bookedSlots[] = $row['time_slot'];  // Fetch booked time slots
    }
    
    echo json_encode($bookedSlots); // Send booked slots as a JSON response
}
?>
