<?php
require_once('../database/db.php');

// Verify the token and retrieve the user_id and user_type
function verifyToken($token)
{
    global $conn;

    // Sanitize the token to prevent SQL injection
    $token = mysqli_real_escape_string($conn, $token);

    // Query the tokens table to check if the token exists
    $sql = "SELECT 
            u.id AS user_id, u.user_type, t.token 
            FROM users u 
            JOIN tokens t 
            ON u.id = t.user_id 
            WHERE t.token = '$token'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Token is valid, retrieve the user_id and user_type
        $row = mysqli_fetch_assoc($result);
        $userData = [
            'user_id' => $row['user_id'],
            'user_type' => $row['user_type'],
        ];

        return $userData;
    } else {
        // Token is invalid or not found
        return null;
    }
}

if (isset($_POST['id']) && isset($_POST['status']) && isset($_POST['token'])) {
    $appointmentId = $_POST['id'];
    $newStatus = $_POST['status'];

    // Assuming you have a valid token in $_POST['token']
    $token = $_POST['token'];
    $userData = verifyToken($token);

    if ($userData !== null) {
        $userId = $userData['user_id'];
        $userType = $userData['user_type'];

        // Check if the user is a vendor or admin
        if ($userType === 'vendor' || $userType === 'admin') {
            // Update the status of the appointment
            $sql = "UPDATE appointments SET status = '$newStatus' WHERE id = '$appointmentId'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Appointment status updated successfully',
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error updating appointment status: ' . mysqli_error($conn),
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized access',
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
