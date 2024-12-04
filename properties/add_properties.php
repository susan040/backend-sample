<?php
session_start();
if(!isset($_SESSION['id'])){
    header("Location: ../index.php");
    exit();
}
require_once ("../database/db.php");

$sqlCategory = "SELECT id, name FROM categories";
$resultCategory = mysqli_query($conn, $sqlCategory);

if(isset($_POST) && isset($_POST['title']) && isset($_POST['category_id']) 
    && isset($_POST['property_status']) && isset($_POST['description']) && isset($_POST['city']) 
    && isset($_POST['district']) && isset($_POST['zip_code'])&& isset($_POST['street_address']) 
    && isset($_POST['total_area']) && isset($_POST['bedroom']) && isset($_POST['bathroom']) 
    && isset($_POST['price'])&& isset($_POST['time_intervel']))
    {
    $title = $_POST['title'];
    $categoryId = $_POST['category_id'];
    $propertyStatus = $_POST['property_status'];
    $description = $_POST['description'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $zipCode = $_POST['zip_code'];
    $streetAddress = $_POST['street_address'];
    $totalArea = $_POST['total_area'];
    $bedroom = $_POST['bedroom'];
    $bathroom = $_POST['bathroom'];
    $price = $_POST['price'];
    $timeIntervel = $_POST['time_intervel'];
    $date = date("Y-m-d h:i:s");

    $sql = "INSERT INTO properties (title, category_id, property_status, 
          description, city, district, zip_code, street_address, 
          total_area, bedroom, bathroom, price, time_intervel, created_at) 
          VALUES ('$title', '$categoryId', '$propertyStatus', '$description', 
          '$city', '$district', '$zipCode', '$streetAddress', '$totalArea', '$bedroom', 
          '$bathroom', '$price', '$timeIntervel', '$date')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $_SESSION['flash_message'] = "Property added successfully!";
        $_SESSION['flash_status'] = "success";
        header("Location: properties.php");
        exit();
    }else {
      $_SESSION['flash_message'] = "Property not added!";
      $_SESSION['flash_status'] = "error";
      header("Location: properties.php");
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
              <!-- Dashboard -->
              <li class="nav-item">
                  <a href="../dashboard.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-home"></i>
                      <p class="ml-2">Dashboard</p>
                  </a>
              </li>
              <!-- Users -->
              <li class="nav-item">
                  <a href="../users/users.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-users"></i>
                      <p class="ml-2">Users</p>
                  </a>
              </li>
              <!-- Properties -->
              <li class="nav-item">
                  <a href="properties.php" class="nav-link bg-secondary text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-key"></i>
                      <p class="ml-2">Properties</p>
                  </a>
              </li>
              <!-- Categories -->
              <li class="nav-item">
                  <a href="../categories/categories.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-layer-group"></i>
                      <p class="ml-2">Categories</p>
                  </a>
              </li>
              <!-- Appointments -->
              <li class="nav-item">
                  <a href="../appointments/appointments.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-calendar"></i>
                      <p class="ml-2">Appointments</p>
                  </a>
              </li>
              <!-- Profile -->
              <li class="nav-item">
                  <a href="../functions/profile.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-user"></i>
                      <p class="ml-2">Profile</p>
                  </a>
              </li>
              <!-- Change Password -->
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
            <h1>Add Properties</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Properties</li>
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
                        <h3 class="card-title">Add Properties</h3>
                    </div>
                    <form role="form" method="POST" action="add_properties.php" enctype="multipart/form-data">
                        <div class="card-body">
                           <div class="form-group">
                            <div class= "form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title"  placeholder="Enter title" required>
                            </div>
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select class="form-control select2bs4" name="category_id" style="width: 100%;">
                                    <?php while ($row = mysqli_fetch_assoc($resultCategory)){?>
                                        <option value=<?php echo $row['id']?> > <?php echo $row['name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="property_status">Property Status</label>
                                <select class="form-control select2bs4" name="property_status" style="width: 100%;">
                                    <option selected></option>
                                    <option value="For Rent">For Rent</option>
                                    <option value="For Sale">For Sale</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea type="text" name="description" class="form-control" id="description"  class="form-control" rows="5" placeholder="Write descriptions" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city"
                                required>
                            </div>
                            <div class="form-group">
                                <label for="district">District</label>
                                <input type="text" class="form-control" id="district" name="district" placeholder="Enter district"
                                required>
                            </div>
                            <div class="form-group">
                                <label for="zip_code">Zip Code</label>
                                <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="Enter zip code"
                                required>
                            </div>
                            <div class="form-group">
                                <label for="street_address">Street Address</label>
                                <input type="text" class="form-control" id="street_address" name="street_address" placeholder="Enter street address"
                                required>
                            </div>    
                            <div class="form-group">
                                <label for="total_area">Total Area</label>
                                <input type="number" class="form-control" id="total_area" name="total_area" placeholder="Enter total area" 
                                required>
                            </div>
                            <div class="form-group">
                                <label for="bedroom">No of Bedroom</label>
                                <input type="number" class="form-control" id="bedroom" name="bedroom" placeholder="Enter no of bedrooms" required>
                            </div>
                            <div class="form-group">
                                <label for="bathroom">No of bathroom</label>
                                <input type="number" class="form-control" id="bathroom" name="bathroom" placeholder="Enter no of bathrooms" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price" placeholder="Enter price" required>
                            </div>
                            <div class="form-group">
                                <label for="time_intervel">Time Intervel</label>
                                <input type="text" class="form-control" id="time_intervel" name="time_intervel" placeholder="Enter Time Intervel" required>
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
