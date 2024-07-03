<?php
// Start session
session_start();

// Include database connection
include 'db.php';

// Check if POST data is received
if(isset($_POST['id'])) {
    // Sanitize input data
    $id = $_POST['id'];

    // Prepare delete query
    $sql = "DELETE FROM reservations WHERE id='$id'";

    // Execute delete query
    if ($conn->query($sql) === TRUE) {
        // Set success message in session
        $_SESSION['success_message'] = 'Reservation deleted successfully';
    } else {
        // Set error message in session
        $_SESSION['error_message'] = "Error deleting reservation: " . $conn->error;
    }
} else {
    // Set error message in session if POST data is not received
    $_SESSION['error_message'] = "Error: POST data not received";
}

// Close the database connection
$conn->close();

// Redirect back to reservation list page
header("Location: reservationList.php");
exit();
?>
