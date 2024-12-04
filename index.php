<?php
session_start();

require_once ('./database/db.php');
//Check if the user is already logged in 
if(isset($_SESSION['id'])){
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Panel | Login page</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="./plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="./dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">


<body class="hold-transition" style="margin-top:100px">
  <div class="wrapper" style="margin-left:550px">
    <div class="container-fluid">
      <div class="row">
        <!-- left column -->
        <div class="col-md-5">
          <div class="card card-secondary">
            <div class="card-header">
            <h3 class="card-title">Login</h3>
            </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="./functions/login.php">
                  <div class="card-body">
                      <div class="form-group">
                      <label for="email">Email address</label>
                      <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                      </div>
                      <div class="form-group">
                      <label for="password">Password</label>
                      <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                      </div>
                  </div>
                  <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-outline-primary">Login In</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
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
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
  $(document).ready(function () {
  bsCustomFileInput.init();
  });
</script>
</body>
</html>
