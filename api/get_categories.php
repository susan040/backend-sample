<?php
require_once('../database/db.php');
require_once('../global.php');

$sql = "SELECT * from categories";
$result = mysqli_query($conn, $sql);
if ($result) {
    $categories= array();
    while($row = mysqli_fetch_assoc($result)){
        $category = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'image' => $image_base.$row['image']
            
        );
        $categories[]= $category;
    }
        echo json_encode([
            'status' => 'success',
            'data' => $categories,
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error category Not found ' . mysqli_error($conn),
        ]);
    }
?>