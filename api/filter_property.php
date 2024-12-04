<?php
require_once('../database/db.php');
require_once('../global.php');

if (
    isset($_POST['property_status']) &&
    isset($_POST['category_id']) && isset($_POST['min_price']) &&
    isset($_POST['max_price']) && isset($_POST['bedroom']) && isset($_POST['bathroom'])
) {
    $searchPropertyStatus = $_POST['property_status'];
    $searchCategoryId = $_POST['category_id'];
    $minPrice = $_POST['min_price'];
    $maxPrice = $_POST['max_price'];
    $bathroom = $_POST['bathroom'];
    $bedroom = $_POST['bedroom'];

    $sql = "SELECT
            p.id,
            p.title,
            p.category_id,
            c.name AS category_name,
            p.description,
            p.city,
            p.district,
            p.zip_code,
            p.street_address,
            p.total_area,
            p.bedroom,
            p.bathroom,
            p.price,
            p.created_at,
            p.time_intervel,
            p.property_status, 
            GROUP_CONCAT(DISTINCT a.id) AS amenity_id,
            GROUP_CONCAT(DISTINCT a.name) AS amenity_name,
            GROUP_CONCAT(DISTINCT a.image) AS amenity_image,
            GROUP_CONCAT(DISTINCT pi.images) AS images,
            GROUP_CONCAT(DISTINCT pi.id) AS image_id
             FROM 
                properties p
            INNER JOIN 
                categories c ON p.category_id = c.id
            LEFT JOIN 
                property_amenities pa ON p.id = pa.property_id
            LEFT JOIN 
                amenities a ON pa.amenity_id = a.id
            LEFT JOIN 
                property_images pi ON p.id = pi.property_id
            WHERE p.property_status = '$searchPropertyStatus'
            AND p.category_id = '$searchCategoryId'
            AND (p.price BETWEEN $minPrice AND $maxPrice
            OR p.bedroom = '$bedroom'
            OR p.bathroom = '$bathroom')
            GROUP BY p.id;
        ";
    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if there are results
    if ($result->num_rows>0) {
        $properties = array();

        // Loop through the results and store details in an array
        while ($row = mysqli_fetch_assoc($result)) {
             $property = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'category_id' => $row['category_id'],
                'category_name' => $row['category_name'],
                'description' => $row['description'],
                'city' => $row['city'],
                'district' => $row['district'],
                'zip_code' => $row['zip_code'],
                'street_address' => $row['street_address'],
                'total_area' => $row['total_area'],
                'bedroom' => $row['bedroom'],
                'bathroom' => $row['bathroom'],
                'price' => $row['price'],
                'property_status'=> $row['property_status'],
                'time_intervel' => $row['time_intervel'],
                'created_at' => $row['created_at'],
                'amenities' => array(),
                'images' => array(),
            );
            $amenityIds = explode(',', $row['amenity_id']);
            $amenityNames = explode(',', $row['amenity_name']);
            $amenityImages = explode(',', $row['amenity_image']);

            $imageIds = explode(',', $row['image_id']);
            $images = explode(',', $row['images']);

            $amenities = array();
            for ($i = 0; $i < count($amenityIds); $i++) {
            // Check if the key exists before accessing it
                if (isset($amenityIds[$i], $amenityNames[$i], $amenityImages[$i])) {
                    $amenity = array(
                        'id' => $amenityIds[$i],
                        'name' => $amenityNames[$i],
                        'image' => $image_base . $amenityImages[$i],
                    );
                    $amenities[$amenityIds[$i]] = $amenity;
                }
            }
            $property['amenities'] = array_values($amenities);
            for ($i = 0; $i < count($imageIds); $i++) {
                $image = array(
                    'imageId' => $imageIds[$i],
                    'images' => $image_base . $images[$i],
                );
                $property['images'][] = $image;
            }
            $properties[] = $property;
        }
        // Output the array as JSON
        echo json_encode([
            'status' => 'success',
            'data' => $properties,
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No results found.',
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Fill up the form',
    ]);
}
?>
