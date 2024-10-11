<?php
session_start();
include('config.php');

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

// Fetch user appointments
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM appointments WHERE user_id = '$user_id' ORDER BY date DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <style>
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions {
            display: flex;
            justify-content: center;
        }
        .actions a {
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .actions a.delete {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Appointments</h1>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <tr>
                    <th>Appointment Type</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td class="actions">
                            <a href="edit_appointment.php?id=<?php echo $row['id']; ?>">Edit</a>
                            <a href="delete_appointment.php?id=<?php echo $row['id']; ?>" class="delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
