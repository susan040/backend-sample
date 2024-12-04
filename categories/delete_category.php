<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

require_once('../database/db.php');

// Check if the user ID is provided
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];

    $deleteCategoriesSql = "DELETE FROM categories WHERE id = '$categoryId'";
    if ($conn->query($deleteCategoriesSql) === TRUE) {
        header("Location: categories.php");
        $_SESSION['flash_message'] = "Categories deleted successfully!";
        $_SESSION['flash_status'] = "success";
    } else {
        $_SESSION['flash_message'] = "Error deleting category";
        $_SESSION['flash_status'] = "error";
    }
} else {
    header("Location: categories.php");
    exit();
}
?>
