<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once('../database/db.php');

// Check if the user ID is provided
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Delete appointments associated with the user first
    $deleteAppointmentsSql = "DELETE FROM appointments WHERE user_id = '$userId'";
    if ($conn->query($deleteAppointmentsSql) === TRUE) {
        // Delete transactions associated with the user next
        $deleteTransactionsSql = "DELETE FROM transaction WHERE user_id = '$userId'";
        if ($conn->query($deleteTransactionsSql) === TRUE) {
            // Both appointments and transactions deleted successfully, now delete the user
            $deleteUserSql = "DELETE FROM users WHERE id = '$userId'";
            if ($conn->query($deleteUserSql) === TRUE) {
                header("Location: users.php");
                $_SESSION['flash_message'] = "User deleted successfully!";
                $_SESSION['flash_status'] = "success";
                exit();
            } else {
                $_SESSION['flash_message'] = "Error deleting user";
                $_SESSION['flash_status'] = "error";
            }
        } else {
            $_SESSION['flash_message'] = "Error deleting transaction";
            $_SESSION['flash_status'] = "error";
        }
    } else {
        $_SESSION['flash_message'] = "Error deleting appointment";
        $_SESSION['flash_status'] = "error";
    }
} else {
    header("Location: users.php");
    exit();
}
?>
