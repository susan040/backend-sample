<?php
require_once("../database/db.php");
require_once("../global.php");

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
        return null;
    }
}

if (
    isset($_POST['property_id']) && isset($_POST['token']) && isset($_POST['start_date'])
    && isset($_POST['end_date']) && isset($_POST['pets_allowed']) 
    && isset($_POST['max_people'])&& isset($_POST['payment_method']) && isset($_POST['amount'])
) {
    $propertyId = $_POST['property_id'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $petsAllowed = $_POST['pets_allowed'];
    $maxPeople = $_POST['max_people'];
    $paymentMethod = $_POST['payment_method'];
    $amount = $_POST['amount'];
    $date = date("Y-m-d h:i:s");

    $token = $_POST['token'];
    $userId = verifyToken($token);

    if ($userId !== null) {
        // Insert rental information into the database
        $sql = "INSERT INTO rental (property_id, user_id, start_date, end_date, status, pets_allowed, max_people, created_at) VALUES 
        ('$propertyId', '$userId', '$startDate','$endDate', 'pending', '$petsAllowed', '$maxPeople', '$date')";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $rentId = mysqli_insert_id($conn);
            $date = date("Y-m-d h:i:s");
            $transactionSql = "INSERT INTO transaction (user_id, rent_id, payment_method, amount, payment_status, date) 
                        VALUES ('$userId', '$rentId', '$paymentMethod', '$amount', 'completed', '$date')";
            $transactionResult = mysqli_query($conn, $transactionSql);

            if($transactionResult){
                $updateRentalSql = "UPDATE rental SET status = 'active' WHERE id = '$rentId'";
                $updateResult = mysqli_query($conn, $updateRentalSql);

                if($updateResult){
                    // Commit transaction
                    mysqli_commit($conn);
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Rent Successful',
                    ]);
                    
                }else{
                    // Rollback transaction if updating rental fails
                    mysqli_rollback($conn);
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Rent is not active',
                    ]);
                }
            }else{
                // Rollback transaction if inserting transaction fails
                mysqli_rollback($conn);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Transaction failed',
                ]);
            }
        } else {
            // Rollback transaction if inserting rental fails
            mysqli_rollback($conn);
            echo json_encode([
                'status' => 'error',
                'message' => 'Rental failed',
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'User authentication failed',
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Fill the form',
    ]);
}


?>
