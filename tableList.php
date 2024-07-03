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

    // Fetch all restaurant tables
    $sql_tables = "SELECT * FROM restaurant_tables";
    $result_tables = $conn->query($sql_tables);

    // Handle form submission to create a new table
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_table'])) {
        $capacity = $_POST['capacity'];
        $description = "Good for $capacity people";

        $sql_create = "INSERT INTO restaurant_tables (capacity, description) VALUES ('$capacity', '$description')";

        if ($conn->query($sql_create) === TRUE) {
            header("Location: tableList.php"); // Refresh the page to see the new table
            exit();
        } else {
            echo "Error: " . $sql_create . "<br>" . $conn->error;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Table Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.2/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }
        .container {
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card {
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-title {
            font-size: 1.5rem;
            color: #007bff;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 4px;
            border-color: #ced4da;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
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
                <a class="nav-link " href="dashboard.php">
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
                <a class="nav-link active" href="tableList.php">
                    <i class="bi bi-table"></i>
                    Table List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>

    <div class="content">
         <!-- Toggle button for sidebar -->
        <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="container">
            <h2 class="mb-4">Table Management</h2>

            <!-- Display Restaurant Tables -->
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Restaurant Tables</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Capacity</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Assuming $result_tables is your result set from database
                                if ($result_tables->num_rows > 0) {
                                    while($row = $result_tables->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["id"] . "</td>";
                                        echo "<td>" . $row["capacity"] . "</td>";
                                        echo "<td>" . $row["description"] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No tables found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form to Create a New Table -->
            <div class="card mt-4">
                <div class="card-body">
                    <h3 class="card-title">Create New Table</h3>
                    <form method="POST" action="tableList.php">
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Capacity</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="create_table">Create Table</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
