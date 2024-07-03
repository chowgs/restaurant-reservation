<?php
    session_start();
    if (isset($_SESSION['loggedin'])) {
        header("Location: dashboard.php");
        exit();
    }

    include 'db.php';

    // Fetch table data from the database
    $tableOptions = '';
    $sql = "SELECT id, description, capacity FROM restaurant_tables";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tableOptions .= "<option value='" . $row['id'] . "'>Table " . $row['id'] . " (" . $row['description'] . " " . $row['capacity'] . " persons)</option>";
        }
    } else {
        $tableOptions .= "<option value=''>No tables available</option>";
    }

    // Get today's date in the format YYYY-MM-DD
    $today = date('Y-m-d');

    $formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Reservation System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('images/bg-image.png'); 
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }
        .reservation-btn {
            margin: 20px 0;
        }
        .modal-content {
            border-radius: 10px;
        }
        .btn-head-reserve {
            background-color: white;
        }
        .btn-head-reserve:hover {
            text-decoration: underline;
        }
        .btn-reserve {
            background-color: #202528;
            color: #fff;
            padding: 18px;
        }
        .btn-reserve:hover {
            background-color: #848484;
            text-decoration: underline;
            color: #fff;
        }
        .auth-links a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        p, h1 {
            color: white;
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #202528;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #848484;
        }
    </style>
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <img src="images/web-logo.png" alt="Restaurant Logo" class="img-fluid" style="max-width: 150px;">
            </div>
            <button class="btn btn-head-reserve d-none d-md-block" data-bs-toggle="modal" data-bs-target="#reservationModal">Make a Reservation</button>
            <div class="auth-links d-none d-md-block">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse d-md-none" id="navbarNav">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link text-white" href="#reservation">Make a Reservation</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="register.php">Register</a></li>
            </ul>
        </div>
    </header>
<br>
    <main class="container text-center mt-5">
        <h1>Welcome to Our Restaurant</h1>
        <p>Book your table now to enjoy an exquisite dining experience.</p>
        <button class="btn btn-reserve" data-bs-toggle="modal" data-bs-target="#reservationModal">Make a Reservation</button>
    </main>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-light">
                <h5 class="modal-title" id="reservationModalLabel">Reservation Form</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_SESSION['success_message'])) {
                    echo '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ' . $_SESSION['success_message'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    unset($_SESSION['success_message']);
                }
                if (isset($_SESSION['error_message'])) {
                    echo '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . $_SESSION['error_message'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    unset($_SESSION['error_message']);
                }
                if (isset($_SESSION['warning_message'])) {
                    echo '
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        ' . $_SESSION['warning_message'] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                    unset($_SESSION['warning_message']);
                }
                ?>
                <form action="reservation.php" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($formData['name']) ? htmlspecialchars($formData['name']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact #:</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo isset($formData['contact']) ? htmlspecialchars($formData['contact']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo isset($formData['address']) ? htmlspecialchars($formData['address']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="table" class="form-label">Table:</label>
                        <select id="table" name="table" class="form-select" required>
                            <option value="">Please select here</option>
                            <?php echo $tableOptions; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="schedule" class="form-label">Date:</label>
                        <input type="date" class="form-control" id="schedule" name="schedule" min="<?php echo $today; ?>" value="<?php echo isset($formData['schedule']) ? htmlspecialchars($formData['schedule']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time Slot:</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="slot1" name="time" value="18:30" <?php echo (isset($formData['time']) && $formData['time'] == '18:30') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="slot1">6:30 PM - 7:30 PM</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="slot2" name="time" value="19:30" <?php echo (isset($formData['time']) && $formData['time'] == '19:30') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="slot2">7:30 PM - 8:30 PM</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="slot3" name="time" value="20:30" <?php echo (isset($formData['time']) && $formData['time'] == '20:30') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="slot3">8:30 PM - 9:30 PM</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="slot4" name="time" value="21:30" <?php echo (isset($formData['time']) && $formData['time'] == '21:30') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="slot4">9:30 PM - 10:30 PM</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="slot5" name="time" value="22:30" <?php echo (isset($formData['time']) && $formData['time'] == '22:30') ? 'checked' : ''; ?> required>
                                <label class="form-check-label" for="slot5">10:30 PM - 11:30 PM</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Reservation</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {
    // Submit reservation form via AJAX
    $('#reservationModal form').submit(function(event) {
        event.preventDefault(); // Prevent default form submission

        var formData = $(this).serialize(); // Serialize form data
        var url = $(this).attr('action'); // Form action URL

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json', // Expect JSON response from server
            success: function(response) {
                if (response.status == 'success') {
                    // Show success message
                    $('#reservationModal .modal-body').html('<div class="alert alert-success">' + response.message + ' Reload the Page</div>');
                } else if (response.status == 'error') {
                    // Show error message
                    $('#reservationModal .modal-body').html('<div class="alert alert-danger">' + response.message + '</div>');
                } else {
                    // Show warning message
                    $('#reservationModal .modal-body').html('<div class="alert alert-warning">' + response.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                // Show error message if AJAX request fails
                $('#reservationModal .modal-body').html('<div class="alert alert-danger">The time slot has already been booked. Reload the Page</div>');
            }
        });
    });
});
</script>
</body>
</html>
