<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

require_once('../database/db.php'); // Include your database connection script

// Get user ID from session
$userId = $_SESSION['id'];

// Query to fetch user's current password
$sql = "SELECT password FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (isset($_POST['old_password'], $_POST['new_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['password'];

        // Verify old password
        if (password_verify($oldPassword, $storedPassword)) {
            // Ensure new password meets criteria (e.g., length)
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update user's password
            $updateSql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($stmt, "si", $hashedPassword, $userId);
            $updateResult = mysqli_stmt_execute($stmt);

            if ($updateResult) {
                // Password changed successfully, log out the user and redirect to login page
                session_destroy(); // Destroy the session
                header("Location: ../index.php"); // Redirect to login page
                exit();
            } else {
                $_SESSION['flash_message'] = "Error changing password.";
                $_SESSION['flash_status'] = "error";
                header("Location: change_password.php");
                exit();
            }
        } else {
            $_SESSION['flash_message'] = "Old password is incorrect!";
            $_SESSION['flash_status'] = "error";
            header("Location: change_password.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Panel | Project name</title>
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
                  <a href="../users/users.php" class="nav-link text-white py-2 rounded hover-effect">
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
                  <a href="categories.php" class="nav-link text-white py-2 rounded hover-effect">
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
                  <a href="change_password.php" class="nav-link bg-secondary text-white py-2 rounded hover-effect">
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
            <h1>Password</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Change Password</li>
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
                    <h3 class="card-title">Change Password</h3>
                    </div>
                    <form role="form" method="POST" action="change_password.php" enctype="multipart/form-data">
                      <div class="card-body">
                          <div class="form-group password-toggle">
                              <label for="old_password">Old Password:</label>
                              <input type="password" id="old_password" name="old_password" placeholder="Enter old password" required>
                              <span class="toggle-icon" onclick="togglePasswordVisibility('old_password')">
                                  <i class="fa fa-eye"></i>
                              </span>
                          </div>
                          <div class="form-group password-toggle">
                              <label for="new_password">New Password:</label>
                              <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                              <span class="toggle-icon" onclick="togglePasswordVisibility('new_password')">
                                  <i class="fa fa-eye"></i>
                              </span>
                          </div>
                      </div>
                      <!-- /.card-body -->
                      <div class="card-footer">
                          <button type="submit" class="btn btn-outline-primary">Submit</button>
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
  <script>
    function togglePasswordVisibility(inputId) {
        var input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }
  </script>
</body>
</html>
