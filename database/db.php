<?php
require_once('config.php');

// Database Connection
$conn = new mysqli($host, $username, $password, $db_name);

// Set the time zone to Nepal (Asia/Kathmandu) in PHP
date_default_timezone_set('Asia/Kathmandu');

// Set the time zone for the current session in MySQL
$conn->query("SET time_zone = '+05:45'");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}