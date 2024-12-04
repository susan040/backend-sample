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
    isset($_POST['property_id']) && isset($_POST['token']) && isset($_POST['rating'])
    && isset($_POST['comment'])) 
    {
    $propertyId = $_POST['property_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $date = date("Y-m-d h:i:s");

    $token = $_POST['token'];
    $userId = verifyToken($token);

    if ($userId !== null) {

        // Check if the user has already reviewed the property
        $sqlCheckReview = "SELECT * FROM reviews WHERE property_id = '$propertyId' AND user_id = '$userId'";
        $resultCheckReview = mysqli_query($conn, $sqlCheckReview);

        if ($resultCheckReview && mysqli_num_rows($resultCheckReview) > 0) {
            // User has already reviewed this property
            echo json_encode([
                'status' => 'error',
                'message' => 'You have already reviewed this property',
            ]);
        } else {
            // Insert review information into the database
            $sqlInsertReview = "INSERT INTO reviews (property_id, user_id, rating, comment, date) VALUES 
            ('$propertyId', '$userId', '$rating', '$comment','$date')";
            $resultInsertReview = mysqli_query($conn, $sqlInsertReview);
            if ($resultInsertReview) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Property Reviewed Successfully',
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Property Not Reviewed',
                ]);
            }
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
