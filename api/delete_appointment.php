<?php

require_once('../database/db.php');

if (isset($_POST['id'])) {
    $appointmentId = $_POST['id']; // Change variable name to $appointmentId

    $updateQuery = "DELETE FROM appointments WHERE id = '$appointmentId'";;
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Appointment Deleted successfully',
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error delete appointment: ' . mysqli_error($conn),
        ]);
    }
}else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Fill the form',
    ]);
}
?>
