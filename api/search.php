<?php
require_once('../database/db.php');
require_once('../global.php');

$property =[];

if (isset($_POST) && isset($_POST['title'])) {
    $search = $_POST['title'];
    $sql = "SELECT * FROM properties WHERE title = '$search'";

    $result = mysqli_query($conn, $sql);


    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            //array_push($users, $row);
            $property[] = $row;
        }
        echo json_encode([
            'status' => 'success',
            'message' => 'Property Fetched successfully',
            'data' => $property,
        ]);
    } else {
        echo json_encode(
            [
                'status' => 'error',
                'message' => 'Fail',
            ]
        );
    }
} else {
    echo json_encode(
        [
            'status' => 'error',
            'message' => 'Fill the form',
        ]
    );
}