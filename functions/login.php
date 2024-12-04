<?php
session_start();
require_once('../database/db.php');

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password']) && $row['user_type'] === 'admin') {
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_type'] = 'admin';

                // Redirect to the admin dashboard
                $_SESSION['flash_message'] = "Login Successful!";
                $_SESSION['flash_status'] = "success";
                header("Location: ../dashboard.php");
                exit();
            } else {
                $_SESSION['flash_message'] = "Invalid credentials!";
                $_SESSION['flash_status'] = "error";
                header("Location: ../index.php?error=invalid_credentials");
                exit();
            }
        }
    } else {
        // User not found
        $_SESSION['flash_message'] = "User not found!";
        $_SESSION['flash_status'] = "error";
        header("Location: ../index.php?error=user_not_found");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
