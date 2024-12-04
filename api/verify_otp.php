<?php
require_once('../database/db.php');

if (isset($_POST['user_id']) && isset($_POST['code'])){
    $user_id = $_POST['user_id'];
    $otp = $_POST['code'];

    // Check if the email and OTP match a user in the database
    $sql = "SELECT * FROM otp where user_id = '$user_id' AND code = '$otp' AND is_verified = 0 order by created_at desc";
    $result = mysqli_query($conn, $sql);

    if($result) {
        $otpObj = mysqli_fetch_assoc($result);

        if($otpObj) {
            $id = $otpObj['id'];
            $updateSql = "UPDATE otp SET is_verified = 1 WHERE id = $id";
            $result1 = mysqli_query($conn, $updateSql);
            echo json_encode([
                'status' => 'success',
                'message' => 'Email Verified Successfully',
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'OTP Verification Failed',
            ]);
        }
    }

    // if ($result->num_rows <= 0) {
    //     $updateSql = "UPDATE otp SET is_verified = 1 WHERE code = '$otp' AND user_id = '$user_id'";
    //     $result1 = mysqli_query($conn, $updateSql);
    //     echo json_encode([
    //         'status' => 'success',
    //         'message' => 'Email Verified Successfully',
    //     ]);
    // } else {
    //     echo json_encode([
    //         'status' => 'error',
    //         'message' => 'Your email is already verified.',
    //     ]);
    // }
} else{
    echo json_encode([
        'status' => 'error',
        'message' => 'Fill up the form',
    ]);
}
?>
