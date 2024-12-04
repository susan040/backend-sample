<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

require_once('../database/db.php');
require_once('../global.php');

if (isset($_POST['name'])) {
    $categoryName = $_POST['name'];
    $date = date("Y-m-d h:i:s");

    // Check if the category already exists
    $checkSql = "SELECT * FROM categories WHERE name = '$categoryName'";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['flash_message'] = "Category already exists!";
        $_SESSION['flash_status'] = "error";
        header("Location: add_category.php");
        exit();
    }

    // Validate and handle image upload
    $filename = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $filename = $_FILES['image']['name'];
        $tempname = $_FILES["image"]["tmp_name"];
        $folder = "../uploads/" . $filename;

        if (!move_uploaded_file($tempname, $folder)) {
            $_SESSION['flash_message'] = "Failed to upload image!";
            $_SESSION['flash_status'] = "error";
            header("Location: add_category.php");
            exit();
        }
    }

    // Prepare and execute the SQL query
    if ($filename !== null) {
        $sql = "INSERT INTO categories (name, image, created_at) VALUES ('$categoryName', '$filename', '$date')";
    } else {
        $sql = "INSERT INTO categories (name, created_at) VALUES ('$categoryName', '$date')";
    }

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['flash_message'] = "Category added successfully!";
        $_SESSION['flash_status'] = "success";
        header("Location: categories.php");
        exit();
    } else {
        $_SESSION['flash_message'] = "Category not added!";
        $_SESSION['flash_status'] = "error";
        header("Location: add_category.php");
        exit();
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
                  <a href="../user/susers.php" class="nav-link text-white py-2 rounded hover-effect">
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
                  <a href="categories.php" class="nav-link bg-secondary text-white py-2 rounded hover-effect">
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
            <h1>Add Category</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Add Category</li>
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
                        <h3 class="card-title">Add Category</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <!-- form start -->
                    <form method="POST" action="add_category.php" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Category Name.." required>
                            </div>
                            <div class="form-group">
                                <label for="image">Category Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>   
                            <!-- /.card-body -->

                            <div class="card-footer">
                            <button type="submit" class="btn btn-outline-primary">Submit</button>
                            </div>
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
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- page script -->
</body>
</html>
