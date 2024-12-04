<?php
// Include database connection
require_once('../database/db.php');
require_once('../global.php');

if(isset($_POST['property_id'])){
    $property_id = $_POST['property_id'];

$query = "SELECT 
            reviews.id AS review_id,
            reviews.rating,
            reviews.comment,
            reviews.date,
            reviews.property_id,
            users.name AS user_name,
            users.image AS user_image
        FROM 
            reviews
        JOIN 
            users ON reviews.user_id = users.id
        WHERE 
            reviews.property_id = $property_id";

$result = mysqli_query($conn, $query);

// Check if there are any reviews found
if ($result && mysqli_num_rows($result) > 0) {
    $reviews = array();
    // Fetch each row as an associative array
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate time difference
        $timestamp = strtotime($row['date']);
        $time_diff = time() - $timestamp;
        $time_ago = '';

        // Convert time difference to human-readable format
        if ($time_diff < 60) {
            $time_ago = 'just now';
        } elseif ($time_diff < 3600) {
            $time_ago = floor($time_diff / 60) . ' minutes ago';
        } elseif ($time_diff < 86400) {
            $time_ago = floor($time_diff / 3600) . ' hours ago';
        } else {
            $time_ago = floor($time_diff / 86400) . ' days ago';
        }

        // Append the $row containing review details along with time ago
        $row['time_ago'] = $time_ago;

        // Concatenate the base URL with each image in the $images array
        $user_images = array_map(function($image) use ($image_base) {
            return $image_base . $image;
        }, explode(',', $row['user_image']));

        // Include the modified 'user_images' field in the row
        $row['user_images'] = $user_images;

        // Remove the 'user_image' field from the row
        unset($row['user_image']);

        $reviews[] = $row;
    }

    // Return reviews' data as JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $reviews
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'data' => 'No reviews found for the given property ID'
    ]); 
}
}else{
    echo json_encode([
        'status' => 'error',
        'message' => 'Fill up the form',
    ]);
}
?>
