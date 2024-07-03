<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    // Redirect unauthorized users to login page
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Query to count the number of pending reservations
$sql_pending = "SELECT COUNT(*) as pending_count FROM reservations WHERE status='pending'";
$result_pending = $conn->query($sql_pending);

// Fetch the result for pending reservations
$pending_count = 0;
if ($result_pending->num_rows > 0) {
    $row_pending = $result_pending->fetch_assoc();
    $pending_count = $row_pending['pending_count'];
}

// Query to count the number of cancelled reservations
$sql_cancelled = "SELECT COUNT(*) as cancelled_count FROM reservations WHERE status='cancelled'";
$result_cancelled = $conn->query($sql_cancelled);

// Fetch the result for cancelled reservations
$cancelled_count = 0;
if ($result_cancelled->num_rows > 0) {
    $row_cancelled = $result_cancelled->fetch_assoc();
    $cancelled_count = $row_cancelled['cancelled_count'];
}

// Query to count the number of arrived reservations
$sql_arrived = "SELECT COUNT(*) as arrived_count FROM reservations WHERE status='arrived'";
$result_arrived = $conn->query($sql_arrived);

// Fetch the result for arrived reservations
$arrived_count = 0;
if ($result_arrived->num_rows > 0) {
    $row_arrived = $result_arrived->fetch_assoc();
    $arrived_count = $row_arrived['arrived_count'];
}

// Query to count the total number of tables
$sql_tables = "SELECT COUNT(*) as table_count FROM restaurant_tables";
$result_tables = $conn->query($sql_tables);

// Fetch the result for total tables
$table_count = 0;
if ($result_tables->num_rows > 0) {
    $row_tables = $result_tables->fetch_assoc();
    $table_count = $row_tables['table_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.2/font/bootstrap-icons.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container-fluid {
            padding-left: 0;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #202528;
            color: white;
            transition: all 0.3s;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            top: 0;
            left: 0;
            z-index: 1000;
            padding-top: 3.5rem;
        }
        #sidebar .nav-link {
            color: white;
            transition: all 0.3s;
            padding: 10px 15px;
            font-size: 1rem;
            text-transform: uppercase;
        }
        #sidebar .nav-link:hover {
            background-color: #495057;
        }
        #sidebar .nav-item .active {
            background-color: #495057;
        }
        #content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: all 0.3s;
        }
        .card-icon {
            font-size: 2rem;
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            #sidebar {
                position: relative;
                height: auto;
                max-height: 100%;
                margin-bottom: 1rem;
            }
            #content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header text-center py-4">
            <h4 class="text-white">Admin Panel</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reservationList.php">
                    <i class="bi bi-list-check"></i>
                    Reservation List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tableList.php">
                    <i class="bi bi-table"></i>
                    Table List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- Page content -->
    <div id="content">
        <!-- Toggle button for sidebar (visible on small screens) -->
        <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Dashboard content -->
        <div class="container mt-4">
            <h2>Welcome to the Dashboard, <?php echo $_SESSION['username']; ?></h2>

            <!-- Cards for statistics -->
            <div class="row mt-4">
                <!-- Pending reservations count -->
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-hourglass-split card-icon text-warning"></i>
                            <h5 class="card-title">Pending Reservations</h5>
                            <p class="card-text">Number of pending reservations:</p>
                            <h3><?php echo $pending_count; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Cancelled reservations count -->
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-x-circle card-icon text-danger"></i>
                            <h5 class="card-title">Cancelled Reservations</h5>
                            <p class="card-text">Number of cancelled reservations:</p>
                            <h3><?php echo $cancelled_count; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Arrived reservations count -->
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle card-icon text-success"></i>
                            <h5 class="card-title">Arrived Reservations</h5>
                            <p class="card-text">Number of arrived reservations:</p>
                            <h3><?php echo $arrived_count; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Total tables count -->
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="bi bi-table card-icon text-info"></i>
                            <h5 class="card-title">Total Tables</h5>
                            <p class="card-text">Number of tables:</p>
                            <h3><?php echo $table_count; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
