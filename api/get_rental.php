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
                r.id AS rental_id,
                r.property_id,
                p.title AS property_title,
                p.property_status,
                p.city,
                p.street_address,
                p.total_area,
                p.bedroom,
                p.bathroom,
                p.price,
                p.description,
                c.name AS category_name,
                u.name AS user_name,
                u.user_type,
                r.user_id,
                r.start_date,
                r.end_date,
                r.status AS rental_status,
                r.max_people,
                r.created_at AS rental_created_at,
                t.id AS transaction_id,
                t.payment_method,
                t.amount AS transaction_amount,
                t.payment_status,
                t.date AS transaction_date,
                GROUP_CONCAT(i.id) AS image_id,
                GROUP_CONCAT(i.images) AS images,
                re.id AS review_id,
                re.rating,
                re.comment,
                re.date AS review_date
            FROM 
                rental r
            JOIN 
                properties p ON r.property_id = p.id
            JOIN 
                transaction t ON r.id = t.rent_id
            JOIN 
                categories c ON p.category_id = c.id
            JOIN 
                users u ON r.user_id = u.id
            JOIN 
                property_images i ON p.id = i.property_id
            LEFT JOIN 
                reviews re ON p.id = re.property_id
            WHERE 
                r.user_id = $userId
            GROUP BY 
                r.id, re.id";

        $result = mysqli_query($conn, $query);

        if($result && mysqli_num_rows($result)>0){
            $rentals = array();
                // Fetch appointment data with property details and images
                while ($row = mysqli_fetch_assoc($result)) {
                    // Modify the 'images' field to include the full image paths
                    $rental = array(
                        'rental_id'=> $row['rental_id'],
                        'property_id'=> $row['property_id'],
                        'property_title'=> $row['property_title'],
                        'property_status' => $row['property_status'],
                        'city'=> $row['city'],
                        'street_address'=> $row['street_address'],
                        'total_area'=> $row['total_area'],
                        'bedroom'=> $row['bedroom'],
                        'bathroom'=> $row['bathroom'],
                        'price'=> $row['price'],
                        'category_name' => $row['category_name'],
                        'user_name' => $row['user_name'],
                        'user_type'=> $row['user_type'],
                        'user_id'=> $row['user_id'],
                        'start_date'=> $row['start_date'],
                        'end_date'=> $row['end_date'],
                        'rental_status'=> $row['rental_status'],
                        'max_people'=> $row['max_people'],
                        'created_at'=> $row['rental_created_at'],
                        'transaction_id'=> $row['transaction_id'],
                        'payment_method'=> $row['payment_method'],
                        'amount'=> $row['transaction_amount'],
                        'payment_status'=> $row['payment_status'],
                        'transaction_date' => $row['transaction_date'],
                        'description' => $row['description'],
                        'review_id' => $row['review_id'],
                        'rating' => $row['rating'],
                        'comment'=> $row['comment'],
                        'review_date'=> $row['review_date'],
                        'images' => array()
                    );
                    $imageIds = explode(',', $row['image_id']);
                    $images = explode(',', $row['images']);
                    for ($i = 0; $i < count($imageIds); $i++) {
                        $image = array(
                            'imageId' => $imageIds[$i],
                            'images' => $image_base . $images[$i],
                        );
                        $rental['images'][] = $image;
                    }

                    $rentals[] = $rental;
                }
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $rentals
            ]);
        }else{
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'No rental found.'
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



