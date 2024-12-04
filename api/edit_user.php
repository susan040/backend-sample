<?php
require_once('../database/db.php');
require_once("../global.php");

// Handle form submission
if (isset($_POST) && isset($_POST['id']) && isset($_POST['name']) && isset($_POST['address']) && isset($_POST['phone'])) {
    // Retrieve the updated user details from the form
    $userId = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $date = date("Y-m-d h:i:s");

    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $imgTmpName = $_FILES['image']['tmp_name'];
        $imgName = $_FILES['image']['name'];
        $imgPath = "../uploads/" . $imgName;

        // Move uploaded image to the uploads directory
        move_uploaded_file($imgTmpName, $imgPath);  

        // Update the user details in the database including the image path
        $updateSql = "UPDATE users SET name = '$name', phone='$phone', address='$address', image = '$imgPath', created_at = '$date' WHERE id = '$userId'";
        $updateResult = mysqli_query($conn, $updateSql);

        if($updateResult){
            // Fetch user data and token associated with the user
            $selectDataSql = "SELECT users.*, tokens.token FROM users LEFT JOIN tokens ON users.id = tokens.user_id WHERE users.id = '$userId'";
            $selectDataResult = mysqli_query($conn, $selectDataSql);
            $userData = mysqli_fetch_assoc($selectDataResult);

            echo json_encode([
                'status' => 'success',
                'message' => 'User edit Successful',
                'data' => $userData
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Fail to edit user with image',
            ]);
        }
    } else {
        // Update the user details in the database without changing the image path
        $updateSql = "UPDATE users SET name = '$name', phone='$phone', address='$address', created_at = '$date' WHERE id = '$userId'";
        $updateResult = mysqli_query($conn, $updateSql);
        if($updateResult){
            // Fetch user data and token associated with the user
            $selectDataSql = "SELECT users.*, tokens.token FROM users LEFT JOIN tokens ON users.id = tokens.user_id WHERE users.id = '$userId'";
            $selectDataResult = mysqli_query($conn, $selectDataSql);
            $userData = mysqli_fetch_assoc($selectDataResult);

            echo json_encode([
                'status' => 'success',
                'message' => 'User edit without image Successful',
                'data' => $userData
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Fail to edit',
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'fill the form'
    ]);
}
?>
