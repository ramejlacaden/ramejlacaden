<?php
session_start();
include('config.php');

// Load Composer's autoloader
require 'vendor/autoload.php'; // Path to PHPMailer's autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit;
}

// Function to send email notifications to users
function sendEmailNotification($recipientEmail, $status, $reason = '') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jnabubos@gmail.com'; // Update with your email
        $mail->Password = 'alxhljezdphvawic'; // Update with your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('jnabubos@gmail.com', 'Admin');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Appointment Status Notification';
        $message = 'Your appointment has been ' . $status . '.';
        if ($status == 'Declined' && !empty($reason)) {
            $message .= '<br><br>Reason: ' . htmlspecialchars($reason);
        }
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Accept or decline appointment based on the action performed by the admin
if (isset($_POST['action']) && isset($_POST['appointment_id'])) {
    $appointment_id = $_POST['appointment_id'];
    $action = $_POST['action'];
    $new_status = ($action == 'accept') ? 'Accepted' : 'Declined';
    $decline_reason = $_POST['decline_reason'] ?? '';

    // Fetch the user's appointment details from the appointment table
    $sql = "SELECT * FROM appointments WHERE id='$appointment_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $recipientEmail = $row['email']; // Fetch email directly from the appointment details

        // Move the appointment to the archive, including the id_picture field
        $archive_sql = "INSERT INTO appointment_archive (user_id, name, age, sex, address, contact_number, appointment_type, fasting_hours, date, time_slot, status, archived_date, id_picture)
                        VALUES ('{$row['user_id']}', '{$row['name']}', '{$row['age']}', '{$row['sex']}', '{$row['address']}','{$row['contact_number']}', '{$row['appointment_type']}', '{$row['fasting_hours']}', '{$row['date']}', '{$row['time_slot']}', '$new_status', NOW(), '{$row['id_picture']}')";
        $archive_result = mysqli_query($conn, $archive_sql);

        if ($archive_result) {
            // Delete the appointment from the appointments table
            $delete_sql = "DELETE FROM appointments WHERE id='$appointment_id'";
            $delete_result = mysqli_query($conn, $delete_sql);

            if ($delete_result) {
                // Send email notification to the user with the reason for decline
                sendEmailNotification($recipientEmail, $new_status, $decline_reason);

                // Redirect back to admin.php with a success message only for decline action
                if ($action == 'decline') {
    header("location: admin.php?message=Submission successful: Decline reason has been sent.");
} else {
    header("location: admin.php?message=Successfully accepted.");
                }
                exit;
            } else {
                echo "Error deleting appointment: " . mysqli_error($conn);
            }
        } else {
            echo "Error archiving appointment: " . mysqli_error($conn);
        }
    } else {
        echo "Error: Appointment not found.";
    }
}

// Fetch pending appointments from the database
$sql = "SELECT * FROM appointments WHERE status='Booked' ORDER BY date DESC";
$appointments = mysqli_query($conn, $sql);

if (!$appointments) {
    die("Error executing query: " . mysqli_error($conn));
}

if (mysqli_num_rows($appointments) == 0) {
    echo "<p class='text-center text-danger'>No booked appointments found.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
        }

        .navbar-custom {
            background-color: #028482;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand {
            color: white;
        }

        .navbar-custom .nav-link:hover {
            color: #b3ffdd;
        }

        .navbar-brand img {
            height: 50px;
            width: auto;
        }

        .container {
            margin-top: 40px;
            max-width: 1200px;
        }

        .card-header {
            background-color: #028482;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        .table-responsive {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #029672;
            border-color: #029672;
        }

        .btn-primary:hover {
            background-color: #026a50;
            border-color: #026a50;
        }

        .btn-decline {
            background-color: #ff4d4d;
            border-color: #ff4d4d;
        }

        .btn-decline:hover {
            background-color: #e60000;
            border-color: #e60000;
        }

        .message-alert {
            margin-top: 15px;
        }

        .btn {
            border-radius: 30px;
            padding: 10px 20px;
        }

        @media (max-width: 768px) {
            .navbar-custom {
                flex-direction: column;
            }

            .container {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">
            <img src="jeda.png" alt="Logo">
        </a>
        <div class="d-flex justify-content-end w-100">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="archive.php">Archive</a></li>
                        <li><a class="dropdown-item" href="manage_services.php">Manage Services</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="text-center">Booked Appointments</h1>

    <?php if (isset($_GET['message'])) : ?>
        <div class="alert alert-success message-alert text-center" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <div class="card mt-4">
        <div class="card-header text-center">
            Manage Appointments
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>User ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Appointment Type</th>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($appointments)) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <form method="POST" action="admin.php" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-primary">Accept</button>
                                    </form>
                                    <button type="button" class="btn btn-decline" data-bs-toggle="modal" data-bs-target="#declineModal<?php echo $row['id']; ?>">
                                        Decline
                                    </button>
                                    <!-- Decline Modal -->
                                    <div class="modal fade" id="declineModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="declineModalLabel">Decline Appointment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="admin.php">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                                        <input type="hidden" name="action" value="decline">
                                                        <div class="form-group">
                                                            <label for="declineReason<?php echo $row['id']; ?>">Reason for Decline</label>
                                                            <textarea class="form-control" id="declineReason<?php echo $row['id']; ?>" name="decline_reason" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-decline">Submit Decline</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- View Button -->
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['id']; ?>">
                                        View
                                    </button>
                                    <!-- Modal for viewing details -->
                                    <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewModalLabel">User Information</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6><strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?></h6>
                                                    <h6><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></h6>
                                                    <h6><strong>Contact Number:</strong> <?php echo htmlspecialchars($row['contact_number']); ?></h6>
                                                    <h6><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></h6>
                                                    <h6><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></h6>
                                                    <h6><strong>Sex:</strong> <?php echo htmlspecialchars($row['sex']); ?></h6>
                                                    <h6><strong>ID Type:</strong> <?php echo htmlspecialchars($row['id_type']); ?></h6>
                                                    <h6><strong>ID Picture:</strong></h6>
                                                    <?php if (!empty($row['id_picture'])) : ?>
                                                        <a href="<?php echo htmlspecialchars($row['id_picture']); ?>" target="_blank" onclick="history.back();"><img src="<?php echo htmlspecialchars($row['id_picture']); ?>" alt="ID Picture" class="img-fluid" width="200"></a>
                                                    <?php else : ?>
                                                        <p>No ID picture uploaded.</p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>