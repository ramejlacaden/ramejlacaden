<?php
session_start();
require 'vendor/autoload.php';
include('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("location: services.php");
    exit;
}

// Fetch user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user email from the database
$email_query = "SELECT email FROM users WHERE id = ?";
$stmt = $conn->prepare($email_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$email_result = $stmt->get_result();
$user_email = '';
if ($email_result->num_rows > 0) {
    $row = $email_result->fetch_assoc();
    $user_email = $row['email'];
}

$message = "";
$message_class = "";
$amount_to_pay = 0; // Initialize to avoid undefined variable warning

// Fetch services from the database for use in the appointment form
$services = mysqli_query($conn, "SELECT * FROM services");

// Appointment booking logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_type = $_POST['appointment_type'] ?? '';
    $date = $_POST['date'] ?? '';
    $time_slot = $_POST['time_slot'] ?? '';
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? '';
    $sex = $_POST['sex'] ?? '';
    $address = $_POST['address'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $email = $user_email;
    $id_type = $_POST['id_type'] ?? '';
    $amount_to_pay = $_POST['amount_to_pay'] ?? 0;

    // Fetch the price for the selected appointment type
    $price_query = "SELECT price FROM services WHERE service_name = ?";
    $stmt = $conn->prepare($price_query);
    $stmt->bind_param("s", $appointment_type);
    $stmt->execute();
    $price_result = $stmt->get_result();
    if ($price_result->num_rows > 0) {
        $row = $price_result->fetch_assoc();
        $amount_to_pay = $row['price'];
    }

    // Check if sex is empty
    if (empty($sex)) {
        $message = "Please select your sex.";
        $message_class = "alert-danger";
    }

    // Default fasting hours based on appointment type
    $fasting_hours = 0;
    if ($appointment_type == 'Laboratory') {
        $fasting_hours = 12;
    } elseif ($appointment_type == '2D Echo') {
        $fasting_hours = 8;
    }

    // Validate required fields
    if (!$appointment_type || !$date || !$time_slot || !$name || !$age || !$sex || !$address || !$contact_number || !$id_type) {
        $message = "Please fill in all the required fields.";
        $message_class = "alert-danger";
    } else {
        // Check if the time slot is already booked for the selected date
        $check_limit_sql = "SELECT * FROM appointments WHERE date = ? AND time_slot = ?";
        $stmt = $conn->prepare($check_limit_sql);
        $stmt->bind_param("ss", $date, $time_slot);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "This time slot is already booked. Please choose a different time.";
            $message_class = "alert-danger";
        } else {
            // Handle ID picture upload and validation
            $id_picture_path = "";
            $picture_match_error = false;
            if (isset($_FILES['id_picture']) && $_FILES['id_picture']['error'] === UPLOAD_ERR_OK) {
                $file_tmp_path = $_FILES['id_picture']['tmp_name'];
                $file_name = $_FILES['id_picture']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                $allowed_file_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($file_ext, $allowed_file_types)) {
                    $upload_file_name = uniqid() . '.' . $file_ext;
                    $upload_file_dir = 'uploads/';
                    $upload_file_path = $upload_file_dir . $upload_file_name;

                    if (move_uploaded_file($file_tmp_path, $upload_file_path)) {
                        $id_picture_path = $upload_file_path;

                        // Validate ID using Tesseract OCR with all required parameters.
                        if (!validateID($id_picture_path, $name, $address, $id_type)) {
                            $message = "Uploaded ID does not match your information or is not valid. Please upload a valid ID.";
                            $message_class = "alert-danger";
                            $picture_match_error = true;
                        }
                    } else {
                        $message = "There was an error uploading the file.";
                        $message_class = "alert-danger";
                    }
                } else {
                    $message = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
                    $message_class = "alert-danger";
                }
            } else {
                $message = "Please upload a valid ID picture.";
                $message_class = "alert-danger";
            }

            // Proceed with booking if no errors occurred
            if (!$picture_match_error && $message_class !== "alert-danger") {
                $insert_sql = "INSERT INTO appointments (user_id, appointment_type, fasting_hours, date, time_slot, name, age, sex, address, contact_number, email, id_type, id_picture, amount_to_pay, status, admin_status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Booked', 'Pending')";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("isissssssssssi", $user_id, $appointment_type, $fasting_hours, $date, $time_slot, $name, $age, $sex, $address, $contact_number, $email, $id_type, $id_picture_path, $amount_to_pay);

                if ($stmt->execute()) {
                    sendConfirmationEmail($email, $name, $appointment_type, $date, $time_slot);
                    sendConfirmationEmail('admin@example.com', 'Admin', $appointment_type, $date, $time_slot);

                    $message = "Appointment booked successfully!";
                    $message_class = "alert-success";
                } else {
                    $message = "Error booking appointment: " . $stmt->error;
                    $message_class = "alert-danger";
                }
                $stmt->close();
            }
        }
    }
}

// Fetch user's appointments
$sql = "SELECT * FROM appointments WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Function to send confirmation email
function sendConfirmationEmail($to, $name, $appointment_type, $date, $time_slot) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jnabubos@gmail.com';
        $mail->Password   = 'alxhljezdphvawic';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('jnabubos@gmail.com', 'Medicalista');
        $mail->addAddress($to, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation';
        $mail->Body = "Dear $name,<br>Your appointment for <strong>$appointment_type</strong> on <strong>$date</strong> at <strong>$time_slot</strong> has been successfully booked.<br>Thank you for choosing Medicalista.<br>Best Regards,<br>Medicalista Team";
        $mail->AltBody = "Dear $name,\n\nYour appointment for $appointment_type on $date at $time_slot has been successfully booked.\n\nThank you for choosing Medicalista.\n\nBest Regards,\nMedicalista Team";

        $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

// Function to extract text from an image using Tesseract and validate against user input
function validateID($id_picture_path, $user_name, $user_address) {
    $tesseractPath = 'tesseract'; // Ensure Tesseract is properly installed and in PATH
    $outputFile = 'uploads/ocr_output'; // Store the output in the uploads folder
    $outputPath = $outputFile . '.txt';

    // Run Tesseract OCR command
    $command = "$tesseractPath " . escapeshellarg($id_picture_path) . " " . escapeshellarg($outputFile) . " 2>&1";
    $output = [];
    $return_var = null;

    // Execute the Tesseract command
    exec($command, $output, $return_var);

    // Check for errors during execution
    if ($return_var !== 0) {
        error_log("Tesseract OCR command failed. Output: " . implode("\n", $output));
        return false;
    }

    // Verify if the OCR output file exists before reading
    if (file_exists($outputPath)) {
        // Read the OCR output
        $ocrText = file_get_contents($outputPath);
        unlink($outputPath); // Clean up the temporary output file

        // Normalize the extracted text for comparison
        $normalizedExtractedText = strtolower(preg_replace('/\s+/', ' ', trim($ocrText)));
        $normalizedExtractedText = preg_replace('/[^a-z0-9 ]/i', '', $normalizedExtractedText);

        // Normalize user-provided details
        $normalizedUserName = strtolower(preg_replace('/[^a-z0-9 ]/i', '', trim($user_name)));
        // Log extracted information for debugging
        error_log("Extracted OCR Text: " . $normalizedExtractedText);
        error_log("User Name for Comparison: " . $normalizedUserName);

        // Check if parts of the name and address are present in the extracted text
        $nameParts = explode(' ', $normalizedUserName);

        $nameMatchCount = 0;
        foreach ($nameParts as $part) {
            if (strpos($normalizedExtractedText, $part) !== false) {
                $nameMatchCount++;
            }
        }

       

        // Define thresholds for matching
        $nameMatchThreshold = count($nameParts) * 0.6; // Require at least 60% of the name parts to match
       
        // Log the results of text matching
        error_log("Name Match Count: $nameMatchCount, Required: $nameMatchThreshold");

        // Return true only if both name and address match the thresholds
        return $nameMatchCount >= $nameMatchThreshold ;
    } else {
        error_log("OCR output file not found at: " . $outputPath);
        return false;
    }
}

?>










<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #d6f2e0;
            font-family: Arial, sans-serif;
        }

        .bg-primary {
            background-color: #028482 !important;
        }

        .nav-link.text-white:hover {
            color: #b3ffdd !important;
        }

        .container {
            margin-top: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .calendar-table {
            width: 100%;
            margin-top: 20px;
            text-align: center;
            background-color: #ffffff;
        }

        .available {
            background-color: #d4edda !important;
            color: #155724 !important;
            border-radius: 5px;
        }

        .fully-booked {
            background-color: #f8d7da !important;
            color: #721c24 !important;
            border-radius: 5px;
        }

        .calendar-day {
            padding: 10px;
            cursor: pointer;
        }

        .month-navigation {
            text-align: center;
            margin-bottom: 20px;
        }

        .month-navigation button {
            background-color: #029672;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .month-navigation span {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .month-navigation button:hover {
            background-color: #026a50;
        }

        .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #28a745;
            color: white;
            border-radius: 10px;
            padding: 2px 5px;
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <header class="bg-primary py-3">
        <nav class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="jeda.png" alt="Medicalista" height="60">
            </div>
            <ul class="nav">
                <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container my-4">
        <?php if (!empty($message)) : ?>
            <div class="alert <?php echo $message_class; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Book Appointment Form -->
        <div id="new-appointment" class="tab-content">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="appointment_type" class="form-label">Appointment Type</label>
                            <select name="appointment_type" id="appointment_type" class="form-select" required>
                                <option value="" disabled selected>Select Appointment Type</option>
                                <?php while ($row = mysqli_fetch_assoc($services)) : ?>
                                    <option value="<?php echo htmlspecialchars($row['service_name']); ?>">
                                        <?php echo htmlspecialchars($row['service_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="time_slot" class="form-label">Time Slot</label>
                            <select name="time_slot" id="time_slot" class="form-select" required>
                                <!-- Time slots will be populated dynamically based on selected date -->
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" name="age" id="age" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="sex" class="form-label">Sex</label>
                            <select name="sex" id="sex" class="form-select" required>
                                <option value="" disabled selected>Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" id="address" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo $user_email; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="id_type" class="form-label">ID Type</label>
                            <select name="id_type" id="id_type" class="form-select" required>
                                <option value="National ID">National ID</option>
                                <option value="Driver's License">Driver's License</option>
                                <option value="Passport">Passport</option>
                                <option value="Voter's ID">Voter's ID</option>
                                <option value="Unified Multi-purpose ID">Unified Multi-purpose ID</option>
                                <option value="SSS Card">SSS Card</option>
                                <option value="Senior Citizen Card">Senior Citizen Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_picture" class="form-label">Upload ID Picture</label>
                            <input type="file" name="id_picture" id="id_picture" class="form-control" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount_to_pay" class="form-label">Amount to Pay</label>
                            <input type="text" name="amount_to_pay" id="amount_to_pay" class="form-control" value="<?php echo $amount_to_pay; ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="submit" class="btn btn-primary" value="Book Appointment">
                </div>
            </form>
        </div>

        <h4 class="mt-5">Check Availability</h4>
        <!-- Month navigation controls -->
        <div class="month-navigation">
            <button id="prev-month">Previous Month</button>
            <span id="month-year"></span>
            <button id="next-month">Next Month</button>
        </div>

        <table class="calendar-table table table-bordered">
            <thead>
                <tr>
                    <th>Sun</th>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                </tr>
            </thead>
            <tbody id="calendar-body">
                <!-- Dynamic Calendar -->
            </tbody>
        </table>
    </div>

    <script>
        function showTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => {
                tab.style.display = tab.id === tabName ? 'block' : 'none';
            });
        }

        document.getElementById('appointment_type').addEventListener('change', function () {
            const selectedOption = this.value;
            if (selectedOption) {
                fetch(`get_service_price.php?service_name=${selectedOption}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('amount_to_pay').value = data.price;
                    })
                    .catch(error => console.error('Error fetching service price:', error));
            }
        });

        document.getElementById('date').addEventListener('change', function () {
            const selectedDate = this.value;
            const timeSlotDropdown = document.getElementById('time_slot');

            // Clear the current dropdown
            timeSlotDropdown.innerHTML = '';

            // Fetch booked time slots for the selected date using AJAX
            fetch(`check_slots.php?date=${selectedDate}`)
                .then(response => response.json())
                .then(bookedSlots => {
                    // Define all available time slots
                    const timeSlots = ['7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM'];

                    // Populate the dropdown with time slots
                    timeSlots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot;

                        // Check if the slot is booked
                        if (bookedSlots.includes(slot)) {
                            option.textContent = `${slot} (Booked)`;
                            option.disabled = true; // Disable the option if booked
                        } else {
                            option.textContent = slot;
                        }

                        timeSlotDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching booked slots:', error));
        });

        // Date navigation variables
        let today = new Date();
        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();

        const monthYearDisplay = document.getElementById('month-year');
        const calendarBody = document.getElementById('calendar-body');

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        // Generate calendar with dynamic month and year
        function generateCalendar(year, month) {
            const firstDay = (new Date(year, month)).getDay();
            const daysInMonth = 32 - new Date(year, month, 32).getDate();

            calendarBody.innerHTML = '';
            let date = 1;

            // Display the current month and year
            monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

            for (let i = 0; i < 6; i++) {
                let row = document.createElement('tr');

                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        let cell = document.createElement('td');
                        row.appendChild(cell);
                    } else if (date > daysInMonth) {
                        break;
                    } else {
                        let cell = document.createElement('td');
                        cell.classList.add('calendar-day');
                        let formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                        cell.setAttribute('data-date', formattedDate);
                        let cellText = document.createTextNode(date);

                        // dropdown for time slots
                        let dropdown = document.createElement('div');
                        dropdown.classList.add('dropdown');

                        let button = document.createElement('button');
                        button.classList.add('btn', 'btn-secondary', 'dropdown-toggle');
                        button.setAttribute('data-bs-toggle', 'dropdown');
                        button.textContent = date;

                        let dropdownMenu = document.createElement('ul');
                        dropdownMenu.classList.add('dropdown-menu');

                        // Fetch booked slots for the specific date
                        fetch(`check_slots.php?date=${formattedDate}`)
                            .then(response => response.json())
                            .then(bookedSlots => {
                                // Calculate available slots
                                const totalSlots = 10;
                                let availableSlots = totalSlots - bookedSlots.length;

                                // Create badge for available slots
                                const badge = document.createElement('span');
                                badge.classList.add('badge');
                                badge.textContent = availableSlots;

                                // Display badge in the cell
                                cell.appendChild(badge);

                                bookedSlots.forEach(slot => {
                                    let li = document.createElement('li');
                                    if (bookedSlots.includes(slot)) {
                                        li.classList.add('fully-booked'); // Mark fully booked
                                        li.textContent = `${slot} (Booked)`;
                                        li.style.color = 'red';
                                    } else {
                                        li.classList.add('available'); // Mark available
                                        li.textContent = slot;
                                        li.style.color = 'green';
                                    }
                                    dropdownMenu.appendChild(li);
                                });
                            });

                        dropdown.appendChild(button);
                        dropdown.appendChild(dropdownMenu);
                        cell.appendChild(dropdown);
                        row.appendChild(cell);
                        date++;
                    }
                }
                calendarBody.appendChild(row);
            }
        }

        // Move to the previous month
        document.getElementById('prev-month').addEventListener('click', function () {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar(currentYear, currentMonth);
        });

        // Move to the next month
        document.getElementById('next-month').addEventListener('click', function () {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar(currentYear, currentMonth);
        });

        // Generate the calendar for the current month and year
        generateCalendar(currentYear, currentMonth);

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
