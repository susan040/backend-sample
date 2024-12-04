<?php
session_start();
if(!isset($_SESSION['id'])){
    header("Location: ../index.php");
    exit();
}
require_once('../database/db.php');
require_once('../global.php');


// Check if category ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}
$userId = $_GET['id'];
// Get category details from the database
$sql = "SELECT * FROM users WHERE id = '$userId'";
$result = mysqli_query($conn, $sql);

if(!$result){
    header("Location: profile.php");
    exit();
}
$user = mysqli_fetch_assoc($result);
// var_dump($category);

//form validation 
if (isset($_POST) && isset($_POST['name']) && isset($_POST['address']) && isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['user_type'])) {
    // Retrieve the updated user details from the form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $type = $_POST['user_type'];
    $date = date("Y-m-d h:i:s");

    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $imgTmpName = $_FILES['image']['tmp_name'];
        $imgName = $_FILES['image']['name'];
        $imgPath = "../uploads/" . $imgName;

        // Move uploaded image to the uploads directory
        move_uploaded_file($imgTmpName, $imgPath);  

       // Update the doctor details in the database including the image path
        $updateSql = "UPDATE users SET 
          name = '$name', 
          address='$address', 
          phone= '$phone',
          email = '$email', 
          user_type = '$type', 
          image = '$imgPath', 
          created_at = '$date'
          WHERE id = '$userId'";
        $upadateResult= mysqli_query($conn, $updateSql);
    }else{
        // Update the doctor details in the database without changing the image path
        $updateSql = "UPDATE users SET 
          name = '$name', 
          address='$address',
          phone= '$phone', 
          email = '$email', 
          user_type = '$type', 
          created_at = '$date'
          WHERE id = '$userId'";
        $updateResult= mysqli_query($conn, $updateSql);
    }
    $_SESSION['flash_message'] = "Edit Successful!!";
    $_SESSION['flash_status'] = "success";
    header("Location: profile.php");
    exit();
  }
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Panel | User Profile</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
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
                  <a href="profile.php" class="nav-link bg-secondary text-white py-2 rounded hover-effect">
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
              <h1>Profile</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Edit Profile</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Profile</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <!-- form start -->
                        <form role="form" method="POST" action="edit_profile.php?id=<?php echo $userId;?>" enctype="multipart/form-data">
                            <div class="card-body">
                                <input type="hidden" name="id" value="<?php echo $user['id'];?>">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter full name.." 
                                    value="<?php echo $user['name'];?>"required>
                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter address .." 
                                    value="<?php echo $user['address'];?>"required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone No</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone no .." 
                                    value="<?php echo $user['phone'];?>"required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email .." 
                                    value="<?php echo $user['email'];?>"required>
                                </div>
                                <div class="form-group">
                                    <label for="name">user type</label>
                                    <input type="user_type" class="form-control" id="user_type" name="user_type" readonly 
                                    value="<?php echo $user['user_type'];?>"required>
                                </div>
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                </div>
                                <div class="form-group">
                                    <img src="../uploads/<?php echo $user['image']; ?>" alt="Img" width="90" height="70"><br>
                                </div>   
                            </div>
                                <div class="card-footer">
                                <button type="submit" class="btn btn-outline-primary">Submit</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
     <footer class="main-footer">
        <strong>Copyright &copy; project name</strong> All rights
        reserved.
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