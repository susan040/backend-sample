<?php

require_once('../database/db.php');
require_once("../global.php");

if (isset($_POST) && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if($row["user_type"] === "customer"){
                if (password_verify($password, $row['password'])) {
                $userId = $row['id'];

                $otpSql = "SELECT * FROM otp WHERE user_id = $userId ORDER BY created_at DESC";
                $otpResultSql = mysqli_query($conn, $otpSql);

                if ($otpResultSql) {
                    $otpDetails = mysqli_fetch_assoc($otpResultSql);

                    if ($otpDetails['is_verified']) {
                        $token = bin2hex(random_bytes(16));
                        $date = date("Y-m-d h:i:s");
                        $tokensql = "INSERT INTO tokens(user_id, token, created_at) VALUES('$userId', '$token', '$date')";
                        $tokenResult = mysqli_query($conn, $tokensql);

                        if ($tokenResult) {
                            // Include the token in the $row array
                            $row['token'] = $token;
                            $row['image'] = $image_base . $row['image'];

                            echo json_encode([
                                'status' => 'success',
                                'message' => 'Login Successful',
                                'data' => $row,
                            ]);
                            return;
                        } else {
                            echo json_encode([
                                'status' => 'error',
                                'message' => 'Token generation failed'
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Please verify your email first'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Please verify your email first'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Account does not exist',
                ]);
            }
            }else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Only customers are allowed to log in',
            ]);
        }
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Account does not exist',
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Fill up the form',
    ]);
}
?>
