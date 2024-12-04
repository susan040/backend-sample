<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

require_once('../database/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; // Add this line

// Use the correct paths for PHPMailer classes
require_once './../PHPMailer/src/PHPMailer.php';
require_once './../PHPMailer/src/SMTP.php';
require_once './../PHPMailer/src/Exception.php'; // Add this line

if (isset($_POST) && isset($_POST['name']) && isset($_POST['address']) && isset($_POST['phone']) 
  && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['user_type'])) { 
  $name = $_POST['name'];
  $address = $_POST['address'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];
  $type = $_POST['user_type'];
  $date = date("Y-m-d h:i:s");


  // Check if the email already exists in the database
  $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
  $emailResult = mysqli_query($conn, $checkEmailSql);

  if ($emailResult && count(mysqli_fetch_all($emailResult)) > 0) {
      $_SESSION['flash_message'] = "Email Already exists!";
      header("Location: add_users.php");
  } elseif ($password !== $confirmPassword) {
      $_SESSION['flash_message'] = "Password does not match";
      header("Location: add_users.php");
  } else {
      // Hash the password
      $hashedConfirmPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);

      // Check if an image file is uploaded
      if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
          $filename = $_FILES["image"]["name"];
          $tempname = $_FILES["image"]["tmp_name"];
          $folder = "../uploads/" . $filename;

          // Move the uploaded image to the specified folder
          if (move_uploaded_file($tempname, $folder)) {
              // Prepare the SQL statement to insert the user into the database with the image
              $insertSql = "INSERT INTO users (name, address, phone, email, password, user_type, image, created_at) 
                            VALUES ('$name','$address','$phone','$email', '$hashedConfirmPassword','$type', '$filename', '$date')";
          }
      } else {
          // Prepare the SQL statement to insert the user into the database without the image
          $insertSql = "INSERT INTO users (name, address, phone, email, password, user_type, created_at) 
                        VALUES ('$name','$address','$phone','$email', '$hashedConfirmPassword','$type', '$date')";
      }

      // Execute the SQL statement
      $result = mysqli_query($conn, $insertSql);

      if ($result) {
          $token = rand(0000, 9999);

          $verify = verificationEmail($email, $token);

          if ($verify) {
              $user_id = $conn->insert_id;
              $date =  date("Y-m-d h:i:sa");
              $tokenSql = "INSERT INTO otp(user_id, code, created_at, is_verified) VALUES ('$user_id','$token','$date','0')";

              $tokenSqlResult = mysqli_query($conn, $tokenSql);

              if ($tokenSqlResult) {
                  $_SESSION['flash_message'] = "OTP Sent Successfully!";
                  $_SESSION['flash_status'] = "success";

                  header("Location: users.php");
              } else {
                  $_SESSION['flash_message'] = "OTP Not Saved!";
                  $_SESSION['flash_status'] = "error";
                  header("Location: add_users.php");
              }
          }
      } else {
          $_SESSION['flash_message'] = "User registration failed!";
          $_SESSION['flash_status'] = "error";
          header("Location: add_users.php");
          exit();
      }
  }
}

function verificationEmail($email, $otp): bool
{
    $emailSubject = 'OTP Verification Code from project';
    $body = "Your OTP code for project is " . $otp;
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '3016ede5089044';
        $mail->Password = 'af1292011dde52';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom("info@project.com");
        $mail->addAddress($email);
        $mail->Subject = $emailSubject;
        $mail->isHTML(false);
        $mail->Body = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo json_encode($e);
        return false;
    }

    return false;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Panel | Project Name</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="../functions/logout.php" role="button">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </li>
    </ul>
          
  </nav>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: black;">
    <!-- Brand Logo -->
    <a href="" class="brand-link">
     <h2>Project name</h2>
    </a>

    <div class="sidebar text-white vh-100 p-3" style="background-color: black;">
    <!-- Sidebar Menu -->
      <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item">
                  <a href="../dashboard.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-home"></i>
                      <p class="ml-2">Dashboard</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="users.php" class="nav-link bg-secondary text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-users"></i>
                      <p class="ml-2">Users</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="../properties/properties.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-key"></i>
                      <p class="ml-2">Properties</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="../categories/categories.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-layer-group"></i>
                      <p class="ml-2">Categories</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="../appointments/appointments.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-calendar"></i>
                      <p class="ml-2">Appointments</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="../functions/profile.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-user"></i>
                      <p class="ml-2">Profile</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="../password/change_password.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-lock"></i>
                      <p class="ml-2">Change Password</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="../rentals/rentals.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-key"></i>
                      <p class="ml-2">Rental</p>
                  </a>
              </li>
          </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>

  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Users</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Add Users</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
   <section class = "content">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                  <?php
                    if (isset($_SESSION['flash_message'])) {
                        if ($_SESSION['flash_status'] == 'success') {
                            ?>
                            <div class="alert alert-success" role="alert">
                                <strong>Hey!</strong> <?php echo $_SESSION['flash_message']; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php
                        } elseif ($_SESSION['flash_status'] == 'error') {
                            ?>
                            <div class="alert alert-danger" role="alert">
                                <strong>Oops!</strong> <?php echo $_SESSION['flash_message']; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php
                        }
                        unset($_SESSION['flash_message']);
                        unset($_SESSION['flash_status']);
                    }
                  ?>
                <div class="card card-secondary">
                    <div class="card-header">
                    <h3 class="card-title">Add Users</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form role="form" method="POST" action="add_users.php" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                              <label for="name">FullName:</label>
                              <input type="name" class="form-control" name="name" id="name" placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                              <label for="address">Address</label>
                              <input type="text" class="form-control" name="address" id="address" placeholder="Enter your address" required>
                            </div>
                            <div class="form-group">
                              <label for="phone">Phone</label>
                              <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter your phone no" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="password" name="confirm_password" placeholder="Enter your confirm password" required>
                            </div>
                            <div class="form-group">
                                <label for="user_type">Type</label>
                                <select class="form-control" id="type" name="user_type" required>
                                    <option value="customer">Customer</option>
                                </select>
                            </div>
                            <div class="form-group">
                              <label for="image">Images:</label>
                              <input type="file" name="image" class="form-control" accept="image/*" placeholder="Choose Image">
                            </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-outline-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- /.content -->
  </div>

  <!-- /.content-wrapper -->
    <footer class="main-footer">
      <strong>Copyright &copy; 2024 Project name</a>.</strong>
      All rights reserved.
    </footer>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="../plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="../plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="../plugins/moment/moment.min.js"></script>
  <script src="../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="../plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="../dist/js/pages/dashboard.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>
</body>
</html>
