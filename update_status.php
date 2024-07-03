<?php
// Start session
session_start();

// Include database connection
include 'db.php';

// Check if POST data is received
if(isset($_POST['id']) && isset($_POST['status'])) {
    // Sanitize input data
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Prepare update query
    $sql = "UPDATE reservations SET status='$status' WHERE id='$id'";

    // Execute update query
    if ($conn->query($sql) === TRUE) {
        // Set success message in session
        $_SESSION['success_message'] = 'Status updated successfully';
    } else {
        // Set error message in session
        $_SESSION['error_message'] = "Error updating status: " . $conn->error;
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
