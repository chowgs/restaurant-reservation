<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 
include 'db.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $table = $_POST['table'];
    $timeSlot = $_POST['time'];
    $schedule = date('Y-m-d H:i:s', strtotime($_POST['schedule']));
    $status = 'pending';

    $_SESSION['form_data'] = $_POST;

    $check_sql = "SELECT * FROM reservations WHERE table_number='$table' AND schedule='$schedule' AND time_slot='$timeSlot'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $_SESSION['error_message'] = 'Error: The selected time slot is not available. Please choose a different time.';
        header('Location: index.php#reservation');
        exit();
    } else {
        $sql = "INSERT INTO reservations (name, email, contact, address, table_number, schedule, time_slot, status) VALUES ('$name', '$email', '$contact', '$address', '$table', '$schedule', '$timeSlot', '$status')";
        if ($conn->query($sql) === TRUE) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pipam027@gmail.com';
                $mail->Password   = 'nfqkdcgyjsjskduc';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->setFrom('pipam027@gmail.com', 'Restaurant Reservation');
                $mail->addAddress($email, $name);
                $mail->isHTML(true);
                $mail->Subject = 'Your Reservation Confirmation';
                $mail->Body    = "
                    <h1>Reservation Confirmation</h1>
                    <p>Dear $name,</p>
                    <p>Thank you for your reservation at our restaurant. Here are your reservation details:</p>
                    <ul>
                        <li><strong>Name:</strong> $name</li>
                        <li><strong>Email:</strong> $email</li>
                        <li><strong>Contact:</strong> $contact</li>
                        <li><strong>Address:</strong> $address</li>
                        <li><strong>Table Number:</strong> $table</li>
                        <li><strong>Schedule:</strong> " . date('F j, Y', strtotime($schedule)) . " at $timeSlot PM</li>
                    </ul>
                    <p>We look forward to serving you!</p>
                    <p>Best Regards,<br>Your Restaurant</p>
                ";
                $mail->send();
               $_SESSION['success_message'] = 'Reserved Successfully, details have been sent to your email!';
                echo json_encode(array("status" => "success", "message" => $_SESSION['success_message']));
                exit();
            } catch (Exception $e) {
                $_SESSION['warning_message'] = 'Reservation successfully submitted but email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
                echo json_encode(array("status" => "warning", "message" => $_SESSION['warning_message']));
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Error: ' . $sql . '<br>' . $conn->error;
            echo json_encode(array("status" => "error", "message" => $_SESSION['error_message']));
            exit();
        }
    }
}
?>
