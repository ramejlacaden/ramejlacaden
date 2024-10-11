<?php
include('config.php');

if (isset($_GET['service_name'])) {
    $service_name = $_GET['service_name'];

    // Query to fetch the price of the selected service
    $query = "SELECT price FROM services WHERE service_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $service_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['price' => $row['price']]);
    } else {
        echo json_encode(['price' => 0]);
    }

    $stmt->close();
} else {
    echo json_encode(['price' => 0]);
}
?>
