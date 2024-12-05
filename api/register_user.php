<?php
require_once('../database/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; // Add this line

// Use the correct paths for PHPMailer classes
require_once './../PHPMailer/src/PHPMailer.php';
require_once './../PHPMailer/src/SMTP.php';
require_once './../PHPMailer/src/Exception.php'; // Add this line

if (isset($_POST)&& isset($_POST['name']) && isset($_POST['address']) && isset($_POST['phone']) && isset($_POST['email']) 
&& isset($_POST['password']) && isset($_POST['confirm_password'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $type = isset($_POST['user_type']) ? $_POST['user_type'] : 'customer';
    $image = isset($_POST['image']) ? $_POST['image'] : '';
    $date = date("Y-m-d h:i:s");
    //$otp = sprintf("%04d", rand(0, 9999));


    // Check if the email already exists in the database
    $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
    $emailResult = mysqli_query($conn, $checkEmailSql);
    if ($emailResult && count(mysqli_fetch_all($emailResult)) > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email already exists.'
        ]);
    } elseif ($password !== $confirmPassword) {
         echo json_encode([
            'status' => 'error',
            'message' => 'Password doesnot match.'
        ]);
    } else {
        // Hash the password
         $hashedConfirmPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);

        // Prepare the SQL statement to insert the user into the database
        $insertSql = "INSERT INTO users (name, address, phone, email, password, user_type, created_at) 
                    VALUES ('$name','$address','$phone','$email', '$hashedConfirmPassword','$type', '$date')";

        $result = mysqli_query($conn, $insertSql);
        

        if($result){
            $token = rand(000000, 999999);

            $verify = verificationEmail($email, $token);

            if($verify) {
                $user_id = $conn->insert_id;
                $date =  date("Y-m-d h:i:sa");
                $tokenSql = "INSERT INTO otp(user_id, code, created_at, is_verified) values('$user_id','$token','$date','0')";

                $tokenSqlResult = mysqli_query($conn, $tokenSql);

                if($tokenSqlResult) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'OTP Sent Successfully', 
                        'user_id' => $user_id
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'OTP Not Saved'
                    ]);
                }
            
            } else {
                echo json_encode([
                        'status' => 'error',
                        'message' => 'OTP send failed',
                    ]);
            }
        }else{
            echo json_encode([
                'status' => 'error',
                'message' => 'Fail',
            ]);
        }

    }

}else{

    echo json_encode([
        'status' => 'error',
        'message' => 'Fill up the form',
    ]);
}




function verificationEmail($email, $otp) : bool 
{
    $emailSubject = 'OTP Verification Code from Project name';
    $body = "Your otp code for email verification is ".$otp;
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail-> SMTPDebug = 0;
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->Username = '3016ede5089044';
        $mail->Password = 'af1292011dde52';
        $mail->SMTPSecure = 'tls';

        $mail->setFrom('project@gmail.com','Project Name');
        $mail->addAddress($email);
        $mail->Subject = $emailSubject;
        $mail->isHTML(false);
        $mail->Body = $body;

        $mail->send();
        return true;

    } catch(Exception $e) {

        echo json_encode($e);
        return false;

    }

    return false;
}


?>
