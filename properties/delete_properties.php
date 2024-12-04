<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

require_once('../database/db.php');

// Check if property ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: properties.php");
    exit();
}

$propertyId = $_GET['id'];

// Delete appointments associated with the property first
$sqlDeleteAppointments = "DELETE FROM appointments WHERE property_id = '$propertyId'";
if ($conn->query($sqlDeleteAppointments)===TRUE) {
    // Appointments deleted successfully, now delete the property
    $sqlDeleteProperty = "DELETE FROM properties WHERE id = '$propertyId'";
    $resultDeleteProperty = mysqli_query($conn, $sqlDeleteProperty);

    if ($resultDeleteProperty) {
        header("Location: properties.php");
        $_SESSION['flash_status'] = "success";
        $_SESSION['flash_message'] = "Property deleted successfully!";
    } else {
        $_SESSION['flash_status'] = "error";
        $_SESSION['flash_message'] = "Error deleting Property: " . mysqli_error($conn);
    }
} else {
    $_SESSION['flash_status'] = "error";
    $_SESSION['flash_message'] = "Error deleting appointments: " . mysqli_error($conn);
}

header("Location: properties.php");
exit();
?>
