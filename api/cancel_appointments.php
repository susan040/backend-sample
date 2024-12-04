<?php
require_once('../database/db.php');

function verifyToken($token)
{
    global $conn;

    $token = mysqli_real_escape_string($conn, $token);

    $sql = "SELECT user_id FROM tokens WHERE token = '$token'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $userId = $row['user_id'];

        return $userId;
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid token',
        ]);
        return null;
    }
}

if (isset($_POST['id']) && isset($_POST['status']) && isset($_POST['token'])) {
    $appointmentId = $_POST['id'];
    $status = $_POST['status'];

    $token = $_POST['token'];
    $userId = verifyToken($token);

    if ($userId !== null) {
        // Check if the appointment belongs to the user
        $checkAppointmentQuery = "SELECT user_id FROM appointments WHERE id = '$appointmentId'";
        $checkAppointmentResult = mysqli_query($conn, $checkAppointmentQuery);

        if ($checkAppointmentResult && mysqli_num_rows($checkAppointmentResult) > 0) {
            $appointmentData = mysqli_fetch_assoc($checkAppointmentResult);
            $appointmentUserId = $appointmentData['user_id'];

            if ($appointmentUserId == $userId) {
                // Update appointment status to 'Cancelled'
                $updateQuery = "UPDATE appointments SET status = '$status' WHERE id = '$appointmentId'";
                $updateResult = mysqli_query($conn, $updateQuery);

                if ($updateResult) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Appointment cancelled successfully',
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error cancelling appointment: ' . mysqli_error($conn),
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Unauthorized: Appointment does not belong to the user',
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error checking appointment ownership',
            ]);
        }

    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid token',
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Fill up the form',
    ]);
}
?>
