<?php
// Include database connection
require_once('../database/db.php');
require_once('../global.php');

$query = "SELECT 
            p.id AS property_id,
            p.title,
            p.category_id,
            c.name AS category_name,
            p.vendor_id,
            u.name AS vendor_name,
            p.property_status,
            p.description,
            p.city,
            p.district,
            p.zip_code,
            p.street_address,
            p.total_area,
            p.bedroom,
            p.bathroom,
            p.price,
            p.time_intervel,
            p.created_at,
            GROUP_CONCAT(DISTINCT pi.images) AS images,
            GROUP_CONCAT(DISTINCT pi.id) AS image_id,
            GROUP_CONCAT(DISTINCT a.id) AS amenity_id,
            GROUP_CONCAT(DISTINCT a.name) AS amenity_name,
            GROUP_CONCAT(DISTINCT a.image) AS amenity_image
        FROM 
            properties p
        JOIN 
            categories c ON p.category_id = c.id
        JOIN 
            users u ON p.vendor_id = u.id
        LEFT JOIN 
            property_amenities pa ON p.id = pa.property_id
        LEFT JOIN 
            amenities a ON pa.amenity_id = a.id
        LEFT JOIN 
            property_images pi ON p.id = pi.property_id
        LEFT JOIN 
            rental r ON p.id = r.property_id
        WHERE 
            (r.id IS NULL OR r.status = 'completed')
        GROUP BY 
            p.id
        ";
$result = mysqli_query($conn, $query);

// Check if there are any properties found
if ($result && mysqli_num_rows($result) > 0) {
    $properties = array();
    //$uniqueImages = array();
    while ($row = mysqli_fetch_assoc($result)) {

      // Add property data to the array
        $property = array(
            'property_id' => $row['property_id'],
            'title' => $row['title'],
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name'],
            'vendor_id' => $row['vendor_id'],
            'vendor_name' => $row['vendor_name'],
            'description' => $row['description'],
            'city' => $row['city'],
            'district' => $row['district'],
            'zip_code' => $row['zip_code'],
            'property_status'=> $row['property_status'],
            'time_intervel'=>$row['time_intervel'],
            'street_address' => $row['street_address'],
            'total_area' => $row['total_area'],
            'bedroom' => $row['bedroom'],
            'bathroom' => $row['bathroom'],
            'price' => $row['price'],
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
            if(isset($images[$i])) {
                $image = array(
                    'id' => $imageIds[$i],
                    'images' => $image_base . $images[$i],
                );
                $property['images'][] = $image;
            }
        }
        $properties[] = $property; // Append the $row containing property details
    }

    // Return properties' data as JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $properties
    ]);
} else {
    // No properties found
    header('Content-Type: application/json');
    echo json_encode(['message' => 'No properties found with the specified rating range.']);
}
// Close the database connection
$conn->close();
?>
