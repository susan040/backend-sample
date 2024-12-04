<?php
// Include database connection
require_once('../database/db.php');
require_once('../global.php');

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

if(isset($_GET['token'])){
    $token = $_GET['token'];
    $userId = verifyToken($token);

    if($userId !== null){
    $query = "SELECT 
                    a.id,
                    a.property_id,
                    a.date,
                    a.time,
                    a.user_id,
                    a.status AS appointment_status,
                    a.created_at,
                    u.name AS user_name,
                    c.name AS category_name,
                    p.title AS property_name,
                    p.property_status,
                    p.city,
                    p.street_address,
                    p.price,
                    u.user_type,
                    GROUP_CONCAT(i.id) AS image_id,
                    GROUP_CONCAT(i.images) AS images
                FROM 
                    appointments a
                JOIN 
                    properties p ON a.property_id = p.id
                JOIN 
                    categories c ON p.category_id = c.id
                JOIN 
                    users u ON a.user_id = u.id
                JOIN 
                    property_images i ON p.id = i.property_id
                WHERE 
                    a.user_id = '$userId'  
                GROUP BY a.id";

        $result = mysqli_query($conn, $query);

        if($result && mysqli_num_rows($result)>0){
            $appointments = array();
                // Fetch appointment data with property details and images
                while ($row = mysqli_fetch_assoc($result)) {
                    // Modify the 'images' field to include the full image paths
                    $appointment = array(
                        'id'=> $row['id'],
                        'property_id'=> $row['property_id'],
                        'property_name'=> $row['property_name'],
                        'date'=> $row['date'],
                        'time'=> $row['time'],
                        'appointment_status'=> $row['appointment_status'],
                        'user_id'=> $row['user_id'],
                        'created_at'=> $row['created_at'],
                        'user_name' => $row['user_name'],
                        'category_name' => $row['category_name'],
                        'property_status' => $row['property_status'],
                        'city'=> $row['city'],
                        'street_address'=> $row['street_address'],
                        'price'=> $row['price'],
                        'user_type'=> $row['user_type'],
                        'images' => array(),
                        
                    );
                    $imageIds = explode(',', $row['image_id']);
                    $images = explode(',', $row['images']);
                    for ($i = 0; $i < count($imageIds); $i++) {
                        $image = array(
                            'imageId' => $imageIds[$i],
                            'images' => $image_base . $images[$i],
                        );
                        $appointment['images'][] = $image;
                    }

                    $appointments[] = $appointment;
                }
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $appointments
            ]);
        }else{
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'No appointment found.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Invalid token.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Token not provided in the headers.'
    ]);
}
?>

