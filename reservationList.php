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

// Fetch reservations data from the database
$sql = "SELECT * FROM reservations";
$result = $conn->query($sql);

// Check for success message
if(isset($_SESSION['success_message'])) {
    // Display success message
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_message'] . '</div>';

    // Unset the success message to prevent it from being displayed again
    unset($_SESSION['success_message']);
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Reservation List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.2/font/bootstrap-icons.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            font-family: Arial, sans-serif;
            flex-direction: column;
        }
        .container-fluid {
            display: flex;
            flex: 1;
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
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 250px);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28255, 255, 255, 1%29' stroke-width='2' linecap='round' linejoin='round' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }
        @media (max-width: 768px) {
            #sidebar {
                position: relative;
                height: auto;
            }
            #content {
                margin-left: 0;
                width: 100%;
            }
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .pending {
            background-color: #ffc107;
            color: #000;
        }

        .done {
            background-color: #28a745;
            color: #fff;
        }

        .completed {
            background-color: #007bff;
            color: #fff;
        }

        .arrived {
            background-color: #17a2b8;
            color: #fff;
        }

        .cancelled {
            background-color: #dc3545;
            color: #fff;
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
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-house-door"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="reservationList.php">
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


    <div id="content" class="flex-grow-1">
        <!-- Toggle button for sidebar -->
        <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Reservation List -->
        <div class="table-responsive mt-4">
            <h2 class="mb-3">Reservation List</h2>  
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Table Number</th>
                        <th>Schedule</th>
                        <th>Time Slot</th>
                        <th>Status</th>
                        <th >Action</th> 
                        <th></th> 

                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are reservations
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>" . $row["contact"] . "</td>";
                            echo "<td>" . $row["table_number"] . "</td>";
                            echo "<td>" . $row["schedule"] . "</td>";
                            echo "<td>" . $row["time_slot"] . "</td>";
                            // Styling the status field
                            echo "<td>";
                            echo "<span class='status-badge " . strtolower($row["status"]) . "'>" . $row["status"] . "</span>";
                            echo "</td>";
                            echo "<td><button type='button' class='btn btn-info' data-bs-toggle='modal' data-bs-target='#reservationModal" . $row["id"] . "'>View</button></td>";
                            echo "<td>";
                            echo "<button type='button' class='btn btn-danger' onclick='confirmDelete(" . $row["id"] . ")'>Delete</button>";
                            echo "</td>";
                            echo "</td>";
                            echo "</tr>";

                            // Modal for each reservation
                            echo "<div class='modal fade' id='reservationModal" . $row["id"] . "' tabindex='-1' aria-labelledby='reservationModalLabel" . $row["id"] . "' aria-hidden='true'>";
                            echo "<div class='modal-dialog modal-dialog-centered'>";
                            echo "<div class='modal-content'>";
                            echo "<div class='modal-header'>";
                            echo "<h5 class='modal-title' id='reservationModalLabel" . $row["id"] . "'>Reservation Details</h5>";
                            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                            echo "</div>";
                            echo "<div class='modal-body'>";
                            // Display reservation details
                            echo "<p><strong>Name:</strong> " . $row["name"] . "</p>";
                            echo "<p><strong>Email:</strong> " . $row["email"] . "</p>";
                            echo "<p><strong>Contact:</strong> " . $row["contact"] . "</p>";
                            echo "<p><strong>Table Number:</strong> " . "Table" . " " . $row["table_number"] . "</p>";
                            echo "<p><strong>Schedule:</strong> " . $row["schedule"] . "</p>";
                            echo "<p><strong>Time Slot:</strong> " . $row["time_slot"] . "</p>";
                            // Status select dropdown
                            echo "<div class='mb-3'>";
                            echo "<label for='statusSelect" . $row["id"] . "' class='form-label'><strong>Status:</strong></label>";
                            echo "<select class='form-select' id='statusSelect" . $row["id"] . "' name='statusSelect" . $row["id"] . "'>";
                            echo "<option value='pending' " . ($row["status"] == "pending" ? "selected" : "") . ">Pending</option>";
                            echo "<option value='done' " . ($row["status"] == "done" ? "selected" : "") . ">Done</option>";
                            echo "<option value='completed' " . ($row["status"] == "completed" ? "selected" : "") . ">Completed</option>";
                            echo "<option value='arrived' " . ($row["status"] == "arrived" ? "selected" : "") . ">Arrived</option>";
                            echo "<option value='cancelled' " . ($row["status"] == "cancelled" ? "selected" : "") . ">Cancelled</option>";
                            echo "</select>";                            
                            echo "</div>";
                            echo "<div class='modal-footer'>";
                            echo "<button type='button' class='btn btn-primary' onclick='updateStatus(" . $row["id"] . ")'>Update Status</button>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";

                        }
                    } else {
                        echo "<tr><td colspan='9'>No reservations found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function updateStatus(id) {
            // Get the selected status
            var status = document.getElementById("statusSelect" + id).value;

            // Send an AJAX request to update the status
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Reload the page to reflect the updated status
                    window.location.reload();
                }
            };
            xhttp.open("POST", "update_status.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("id=" + id + "&status=" + status);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this reservation?")) {
                // If user confirms, proceed with deletion
                deleteReservation(id);
            }
        }

        function deleteReservation(id) {
            // Send an AJAX request to delete the reservation
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Reload the page to reflect the updated status
                    window.location.reload();
                }
            };
            xhttp.open("POST", "delete_reservation.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("id=" + id);
        }
    </script>

</body>
</html>
