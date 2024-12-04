<?php
require_once('../database/db.php');

// Verify the token and retrieve the user_id
function verifyToken($token)
{
    global $conn;

    // Sanitize the token to prevent SQL injection
    $token = mysqli_real_escape_string($conn, $token);

    // Query the tokens table to check if the token exists
    $sql = "SELECT user_id FROM tokens WHERE token = '$token'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Token is valid, retrieve the user_id
        $row = mysqli_fetch_assoc($result);
        $userId = $row['user_id'];

        return $userId;
    } else {
        // Token is invalid or not found
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid token',
        ]);
        return null;
    }
}

if (isset($_POST['property_id']) && isset($_POST['date']) && isset($_POST['status']) && isset($_POST['token']) && isset($_POST['time'])) {
    $propertyId = $_POST['property_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];
    $time = $_POST['time'];
    $date = date("Y-m-d h:i:s");

    // Assuming you have a valid token in $_POST['token']
    $token = $_POST['token'];
    $userId = verifyToken($token);

    if ($userId !== null) {
        // Check if the same customer has booked the same property before
        $checkExistingAppointmentQuery = "SELECT * FROM appointments WHERE property_id = '$propertyId' AND status = 'pending' AND user_id = '$userId'";
        $existingAppointmentResult = mysqli_query($conn, $checkExistingAppointmentQuery);

        if ($existingAppointmentResult && mysqli_num_rows($existingAppointmentResult) > 0) {
            // The customer has already booked the same property
            echo json_encode([
                'status' => 'error',
                'message' => 'You have already booked this property',
            ]);
        } else {
            // Check if property is for sale
            $propertyStatusQuery = "SELECT property_status FROM properties WHERE id = '$propertyId'";
            $propertyStatusResult = mysqli_query($conn, $propertyStatusQuery);

            if ($propertyStatusResult && mysqli_num_rows($propertyStatusResult) > 0) {
                $propertyStatusRow = mysqli_fetch_assoc($propertyStatusResult);

                if ($propertyStatusRow['property_status'] === 'For Sale') {
                    // Property is for sale, proceed to create appointment
                    $sql = "INSERT INTO appointments (property_id, date, time, status, user_id, created_at) VALUES ('$propertyId', '$date', '$time', '$status', '$userId', '$date')";
                    $result = mysqli_query($conn, $sql);

                    if ($result) {
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Appointment created successfully',
                        ]);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Error creating appointment: ' . mysqli_error($conn),
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Property is not for sale',
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error fetching property status',
                ]);
            }
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
