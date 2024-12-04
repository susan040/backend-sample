<?php
require_once('database/db.php');
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$totalUserSql = "SELECT COUNT(*) AS total_users FROM users";
$totalAppointmentSql = "SELECT COUNT(*) AS total_appointments FROM appointments";
$totalPropertySql = "SELECT COUNT(*) AS total_properties FROM properties";
$totalrentalSql = "SELECT COUNT(*) AS total_rentals FROM rental";

$userResult = mysqli_query($conn, $totalUserSql);
$appointmentResult = mysqli_query($conn, $totalAppointmentSql);
$propertyResult = mysqli_query($conn, $totalPropertySql);
$rentalResult = mysqli_query($conn, $totalrentalSql);

// SQL query to fetch total property rent by month
$sql = "SELECT MONTH(start_date) AS month, 
               SUM(properties.price * DATEDIFF(end_date, start_date)) AS total_rent
        FROM rental
        INNER JOIN properties ON rental.property_id = properties.id
        WHERE YEAR(start_date) = YEAR(CURDATE()) -- Filter by current year
        GROUP BY MONTH(start_date)";

$result = mysqli_query($conn, $sql);

// Format data for Morris.js
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $month = date("F", mktime(0, 0, 0, $row['month'], 1));
    $data[] = array('label' => $month, 'value' => $row['total_rent']);
}

// Convert data to JSON for Morris.js
$jsonData = json_encode($data);

// Fetch monthly rental data
$sql_monthly_rental = "SELECT MONTH(start_date) AS month, COUNT(*) AS count FROM rental GROUP BY MONTH(start_date)";
$result_monthly_rental = $conn->query($sql_monthly_rental);

$monthly_rental_data = [];
while ($row = $result_monthly_rental->fetch_assoc()) {
    $monthly_rental_data[$row['month']] = $row['count'];
}

// Fetch monthly transaction data
$sql_monthly_transaction = "SELECT MONTH(date) AS month, COUNT(*) AS count FROM transaction GROUP BY MONTH(date)";
$result_monthly_transaction = $conn->query($sql_monthly_transaction);

$monthly_transaction_data = [];
while ($row = $result_monthly_transaction->fetch_assoc()) {
    $monthly_transaction_data[$row['month']] = $row['count'];
}

// Fetch payment method data
$sql_payment_method = "SELECT payment_method, COUNT(*) AS count FROM transaction GROUP BY payment_method";
$result_payment_method = $conn->query($sql_payment_method);

$payment_method_data = [];
while ($row = $result_payment_method->fetch_assoc()) {
    $payment_method_data[$row['payment_method']] = $row['count'];
}

// Fetch appointment status data
$sql_appointment_status = "SELECT status, COUNT(*) AS count FROM appointments GROUP BY status";
$result_appointment_status = $conn->query($sql_appointment_status);

$appointment_status_data = [];
while ($row = $result_appointment_status->fetch_assoc()) {
    $appointment_status_data[$row['status']] = $row['count'];
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin Panel| Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <style>
    .chart-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        width: 100%;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .row {
        margin-left: 0;
        margin-right: 0;
        margin-top:10px;
        margin-bottom:20px;
    }

    .col-lg-6,
    .col-md-6,
    .col-12 {
        display: flex;
        justify-content: center;
    }

    .mb-4 {
        margin-bottom: 1.5rem !important;
    }

    canvas {
        display: block;
        margin: 0 auto;
    }

    /* Additional mobile responsiveness */
    @media (max-width: 767px) {
        .col-lg-6, .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .col-md-6, .col-lg-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
  </style>
   
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
          <a class="nav-link" href="./functions/logout.php" role="button">
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
                  <a href="dashboard.php" class="nav-link bg-secondary text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-home"></i>
                      <p class="ml-2">Dashboard</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="users/users.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-users"></i>
                      <p class="ml-2">Users</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="properties/properties.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-key"></i>
                      <p class="ml-2">Properties</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="categories/categories.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-layer-group"></i>
                      <p class="ml-2">Categories</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="appointments/appointments.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-calendar"></i>
                      <p class="ml-2">Appointments</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="functions/profile.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-user"></i>
                      <p class="ml-2">Profile</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="password/change_password.php" class="nav-link text-white py-2 rounded hover-effect">
                      <i class="nav-icon fas fa-lock"></i>
                      <p class="ml-2">Change Password</p>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="rentals/rentals.php" class="nav-link text-white py-2 rounded hover-effect">
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
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
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
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3>
                    <?php
                      // Display the total number of users
                      $totalUsers = mysqli_fetch_assoc($userResult)['total_users'];
                      echo $totalUsers;
                      ?>
                  </h3>

                  <p>Total User</p>
                </div>
                <div class="icon">
                  <i class="fas fa-users"></i>
                </div>
                <a href="./users/users.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-dark">
                <div class="inner">
                  <h3>
                    <?php
                      // Display the total number of users
                      $totalAppointments = mysqli_fetch_assoc($appointmentResult)['total_appointments'];
                      echo $totalAppointments;
                    ?>
                  </h3>

                  <p>Total Appointments</p>
                </div>
                <div class="icon">
                  <i class="fas fa-calendar"></i>
                </div>
                <a href="./appointments/appointments.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3>
                    <?php
                      // Display the total number of users
                      $totalProperties = mysqli_fetch_assoc($propertyResult)['total_properties'];
                      echo $totalProperties;
                    ?>
                  </h3>

                  <p>Total Properties</p>
                </div>
                <div class="icon">
                  <i class="fas fa-home"></i>
                </div>
                <a href="./properties/properties.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-primary">
                <div class="inner">
                  <h3>
                    <?php
                      // Display the total number of users
                      $totalRentals = mysqli_fetch_assoc($rentalResult)['total_rentals'];
                      echo $totalRentals;
                    ?>
                  </h3>

                  <p>Rental</p>
                </div>
                <div class="icon">
                  <i class="fas fa-key"></i>
                </div>
                <a href="./rentals/rentals.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          </div>
          
          <div class="row">
            <!-- Monthly Rental Chart -->
            <div class="col-lg-6 col-12 mb-6">
                <div class="chart-container">
                    <canvas id="monthlyRentalChart" width="300" height="250"></canvas>
                </div>
            </div>            
            <div class="col-lg-6 col-12 mb-6">
                <div class="chart-container">
                    <canvas id="monthlyTransactionChart" width="300" height="250"></canvas>
                </div>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-lg-6 col-12 mb-6">
                <div>
                    <canvas id="paymentMethodChart" width="450" height="450"></canvas>
                </div>
            </div>           
            <div class="col-lg-6 col-12 mb-6">
                <div>
                    <canvas id="appointmentStatusChart" width="450" height="450"></canvas>
                </div>
            </div>
          </div>

        <script>
        function createMonthlyRentalChart() {
            var monthlyRentalData = <?php echo json_encode($monthly_rental_data); ?>;
            var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var ctx = document.getElementById('monthlyRentalChart').getContext('2d');
            var monthlyRentalChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(monthlyRentalData).map(function(month) {
                        return months[parseInt(month) - 1];
                    }),
                    datasets: [{
                        label: 'Monthly Rental Report',
                        data: Object.values(monthlyRentalData),
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
          }
          function createMonthlyTransactionChart() {
              var monthlyTransactionData = <?php echo json_encode($monthly_transaction_data); ?>;

              var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

              var ctx = document.getElementById('monthlyTransactionChart').getContext('2d');
              var monthlyTransactionChart = new Chart(ctx, {
                  type: 'line', 
                  data: {
                      labels: Object.keys(monthlyTransactionData).map(function(month) {
                          return months[parseInt(month) - 1];
                      }),
                      datasets: [{
                          label: 'Monthly Transaction Report',
                          data: Object.values(monthlyTransactionData),
                          backgroundColor: 'rgba(54, 162, 235, 0.2)',
                          borderColor: 'rgba(54, 162, 235, 1)',
                          borderWidth: 2,
                          fill: false, 
                          lineTension: 0.1 
                      }]
                  },
                  options: {
                      scales: {
                          y: {
                              beginAtZero: true
                          }
                      },
                      elements: {
                          point: {
                              radius: 5, 
                              backgroundColor: 'rgba(54, 162, 235, 1)'
                          }
                      }
                  }
              });
          }
            document.addEventListener('DOMContentLoaded', function() {
                createMonthlyRentalChart();
                createMonthlyTransactionChart();
            });

            function createPaymentMethodChart() {
              var paymentMethodData = <?php echo json_encode($payment_method_data); ?>;

              var ctx = document.getElementById('paymentMethodChart').getContext('2d');
              var paymentMethodChart = new Chart(ctx, {
                  type: 'doughnut',  
                  data: {
                      labels: Object.keys(paymentMethodData),
                      datasets: [{
                          data: Object.values(paymentMethodData),
                          backgroundColor: [
                              'rgba(34, 139, 34, 0.5)',  // Dark green
                              'rgba(128, 0, 128, 0.5)',  // Dark purple
                              'rgba(0, 100, 0, 0.5)',    // Dark forest green
                              'rgba(139, 69, 19, 0.5)',  // Dark brown
                          ],
                          borderColor: [
                              'rgba(34, 139, 34, 1)',    // Dark green
                              'rgba(128, 0, 128, 1)',    // Dark purple
                              'rgba(0, 100, 0, 1)',      // Dark forest green
                              'rgba(139, 69, 19, 1)',    // Dark brown
                          ],

                          borderWidth: 1
                      }]
                  },
                  options: {
                      responsive: true,  
                      legend: {
                          position: 'right',
                      },
                      cutoutPercentage: 50,  
                  }
              });
          }


          function createAppointmentStatusChart() {
              var appointmentStatusData = <?php echo json_encode($appointment_status_data); ?>;

              var ctx = document.getElementById('appointmentStatusChart').getContext('2d');
              var appointmentStatusChart = new Chart(ctx, {
                  type: 'pie',
                  data: {
                      labels: Object.keys(appointmentStatusData),
                      datasets: [{
                          data: Object.values(appointmentStatusData),

                          backgroundColor: [
                              'rgba(75, 0, 130, 0.5)',  // Indigo
                              'rgba(0, 0, 139, 0.5)',   // Dark Blue
                              'rgba(139, 69, 19, 0.5)', // Dark Orange
                              'rgba(0, 128, 128, 0.5)', // Teal
                          ],
                          borderColor: [
                              'rgba(75, 0, 130, 1)',    // Indigo
                              'rgba(0, 0, 139, 1)',     // Dark Blue
                              'rgba(139, 69, 19, 1)',   // Dark Orange
                              'rgba(0, 128, 128, 1)',   // Teal
                          ],
                          
                          borderWidth: 1
                      }]
                  },
                  options: {
                      responsive: false,
                      legend: {
                          position: 'right',
                      }
                  }
              });
          }
          document.addEventListener('DOMContentLoaded', function() {
              createPaymentMethodChart();
              createAppointmentStatusChart();
          });
            </script>
        </section>
    </section>
              
        
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
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="plugins/moment/moment.min.js"></script>
  <script src="plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="dist/js/pages/dashboard.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
</body>
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

  $(function () {
    // Use Morris.js to render the chart
    Morris.Bar({
        element: 'revenue-chart-canvas',
        data: <?php echo $jsonData; ?>,
        xkey: 'label',
        ykeys: ['value'],
        labels: ['Total Rent'],
        hideHover: 'auto',
        resize: true
    });
});
</script>
</html>