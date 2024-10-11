<?php
session_start();
include('config.php');

// Redirect to login page if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("location: admin_login.php");
    exit;
}

// Get the current year and month if not set
if (!isset($_GET['month']) || !isset($_GET['year'])) {
    $current_month = date('m');
    $current_year = date('Y');
} else {
    $current_month = $_GET['month'];
    $current_year = $_GET['year'];
}

// Initialize search query variable
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_query = mysqli_real_escape_string($conn, $search_query);
}

// Fetch archived accepted appointments grouped by month with search functionality
$sql_accepted = "SELECT *, DATE_FORMAT(archived_date, '%Y-%m') AS archive_month 
        FROM appointment_archive 
        WHERE status = 'Accepted'
        AND DATE_FORMAT(archived_date, '%Y-%m') = '$current_year-$current_month'
        AND (contact_number LIKE '%$search_query%' 
            OR appointment_type LIKE '%$search_query%' 
            OR date LIKE '%$search_query%' 
            OR time_slot LIKE '%$search_query%' 
            OR name LIKE '%$search_query%') 
        ORDER BY archived_date DESC";

$accepted_archives = mysqli_query($conn, $sql_accepted);

// Fetch archived declined appointments grouped by month with search functionality
$sql_declined = "SELECT *, DATE_FORMAT(archived_date, '%Y-%m') AS archive_month 
        FROM appointment_archive 
        WHERE status = 'Declined'
        AND DATE_FORMAT(archived_date, '%Y-%m') = '$current_year-$current_month'
        AND (contact_number LIKE '%$search_query%' 
            OR appointment_type LIKE '%$search_query%' 
            OR date LIKE '%$search_query%' 
            OR time_slot LIKE '%$search_query%' 
            OR name LIKE '%$search_query%') 
        ORDER BY archived_date DESC";

$declined_archives = mysqli_query($conn, $sql_declined);

// Function to get previous and next months
function getPreviousMonth($current_month, $current_year) {
    if ($current_month == 1) {
        return ['month' => 12, 'year' => $current_year - 1];
    }
    return ['month' => $current_month - 1, 'year' => $current_year];
}

function getNextMonth($current_month, $current_year) {
    if ($current_month == 12) {
        return ['month' => 1, 'year' => $current_year + 1];
    }
    return ['month' => $current_month + 1, 'year' => $current_year];
}

// Get previous and next months
$previous_month_info = getPreviousMonth($current_month, $current_year);
$next_month_info = getNextMonth($current_month, $current_year);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #d6f2e0;
        }

        .container {
            margin-top: 50px;
        }

        h1,
        h2 {
            text-align: center;
            color: #333;
        }

        /* Navbar adjustments */
        .navbar-custom {
            background-color: #028482;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand {
            color: white;
        }

        .navbar-custom .nav-link:hover {
            color: #b3ffdd;
        }

        /* Increase logo size */
        .navbar-brand img {
            height: 70px; /* Larger logo height */
            width: auto; /* Keep the aspect ratio */
        }

        /* Search bar styling */
        .search-bar .form-control {
            border-radius: 20px 0 0 20px;
            padding: 0.5rem 1rem;
            width: 250px;
            border: 1px solid #ced4da;
            background-color: #eaf4f0;
            color: #333;
            height: 40px;
            box-shadow: none;
        }

        .search-bar .btn-primary {
            border-radius: 0 20px 20px 0;
            background-color: #029672;
            border: none;
            padding: 0.5rem 1rem;
            height: 40px;
            display: flex;
            align-items: center;
            box-shadow: none;
            margin-left: -1px;
        }

        /* Month Dropdown */
        .month-dropdown {
            margin-left: 20px;
        }

        table {
            margin-top: 20px;
            font-size: 16px;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #029672;
            border-color: #029672;
        }

        .btn-primary:hover {
            background-color: #026a50;
            border-color: #026a50;
        }

        .card-header {
            background-color: #028482;
            color: white;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand" href="admin.php">
            <img src="jeda.png" alt="Logo" style="height: 60px;"> <!-- Logo -->
        </a>
        <div class="d-flex justify-content-end w-100">
            <!-- Search Bar first -->
            <form method="GET" class="d-flex search-bar">
                <input class="form-control" type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search...">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
            <!-- Then the Admin and Logout buttons -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="admin.php">Admin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Archived Appointments</h1>

    <div class="d-flex justify-content-center">
        <!-- Previous Month Button -->
        <a href="?month=<?php echo $previous_month_info['month']; ?>&year=<?php echo $previous_month_info['year']; ?>" class="btn btn-primary">Previous Month</a>

        <!-- Dropdown to Select Month -->
        <div class="month-dropdown">
            <select class="form-select" onchange="location.href='?month='+this.value+'&year=<?php echo $current_year; ?>'">
                <?php
                $months = [
                    '01' => 'January',
                    '02' => 'February',
                    '03' => 'March',
                    '04' => 'April',
                    '05' => 'May',
                    '06' => 'June',
                    '07' => 'July',
                    '08' => 'August',
                    '09' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December'
                ];
                foreach ($months as $num => $name) {
                    $selected = ($num == $current_month) ? 'selected' : '';
                    echo "<option value='$num' $selected>$name</option>";
                }
                ?>
            </select>
        </div>

        <!-- Next Month Button -->
        <a href="?month=<?php echo $next_month_info['month']; ?>&year=<?php echo $next_month_info['year']; ?>" class="btn btn-primary">Next Month</a>
    </div>

    <!-- Accepted Appointments -->
    <div class="card mt-4">
        <div class="card-header">Accepted Appointments</div>
        <div class="card-body">
            <?php if (mysqli_num_rows($accepted_archives) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Contact Number</th>
                                <th>Name</th>
                                <th>Appointment Type</th>
                                <th>Fasting Hours</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Status</th>
                                <th>Archived Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($accepted_archives)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['appointment_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fasting_hours']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['archived_date']); ?></td>
                                    <td>
                                        <a href="medical_results.php?appointment_id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-success">Create Medical Results</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-center text-danger">No accepted archived appointments found for this month.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Declined Appointments -->
    <div class="card mt-4">
        <div class="card-header">Declined Appointments</div>
        <div class="card-body">
            <?php if (mysqli_num_rows($declined_archives) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Contact Number</th>
                                <th>Name</th>
                                <th>Appointment Type</th>
                                <th>Fasting Hours</th>
                                <th>Date</th>
                                <th>Time Slot</th>
                                <th>Status</th>
                                <th>Archived Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($declined_archives)) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['appointment_type']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fasting_hours']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['archived_date']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-center text-danger">No declined archived appointments found for this month.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
