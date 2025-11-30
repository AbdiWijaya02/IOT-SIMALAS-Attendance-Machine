<?php
session_start();

include("classes/connect.php");
include("classes/login.php");


// Cek apakah user sudah login
if (isset($_SESSION["aiot_userid"]) && is_numeric($_SESSION["aiot_userid"])) {

    $id = $_SESSION["aiot_userid"];
    $login = new Login();
    $result = $login->check_login($id);

    if ($result) {
        // Session valid, ambil data user
        $userid = $_SESSION["aiot_userid"];
        $username = $_SESSION["aiot_nama"];
        $userNIM = $_SESSION["aiot_NIM"];
        $userPBL = $_SESSION["aiot_PBL"];
        $useremail = $_SESSION["aiot_email"];

        // Mencegah tampilan cache setelah logout
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
    } else {
        // Session tidak valid
        header("Location: login.php");
        exit();
    }
} else {
    // Belum login
    header("Location: login.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <title>Simalas - Kehadiran</title>
    <link href="assets/img/brail2.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <img src="assets/img/brail2.png" alt="custom icon" width="40" height="40">
                </div>
                <div class="sidebar-brand-text mx-3">Simalas <sup>2025</sup></div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- <hr class="sidebar-divider">
            <div class="sidebar-heading">
                Addons
            </div> -->

            <!-- Nav Item - Charts -->
            <li class="nav-item active">
                <a class="nav-link" href="Kehadiran.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Kehadiran</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="admin.php">
                    <i class="fa fa-users"></i>
                    <span>Admin</span></a>
            </li> -->

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>


        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow d-flex justify-content-between">
                    <!-- KIRI: Time Display -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="#" id="timeDisplay" style="
                            border: 2px solid #f4f4f4;
                            border-radius: 12px;
                            padding: 12px 20px;
                            font-size: 18px;
                            color: rgb(15, 15, 15);
                            background-color: rgb(#f4f4f4);
                            transition: all 0.3s ease;
                        " onmouseover="this.style.backgroundColor='#e6f0ff'" onmouseout="this.style.backgroundColor='#f0f8ff'">
                                <i class="fas fa-clock me-2"></i>
                                <span id="timeText">TIME</span>
                            </a>
                        </li>
                    </ul>

                    <!-- KANAN: Menu lainnya -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Alerts Center</h6>
                                <!-- Alert items here -->
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">Message Center</h6>
                                <!-- Message items here -->
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($username); ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Rekap Kehadiran</h1>
                        <a href="#" id="generateReport" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>

                    </div>
                    <!-- Content Row: Presentasi dan Donut -->
                    <div class="row">
                        <!-- Presentasi Kehadiran (Progress bar) -->
                        <div class="col-xl-8 col-lg-7 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Presentasi Kehadiran</h6>
                                </div>
                                <div class="card-body">
                                    <h4 class="small font-weight-bold">Absent <span class="float-right" id="absentText">0%</span></h4>
                                    <div class="progress mb-4">
                                        <div id="absentBar" class="progress-bar bg-danger" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>

                                    <h4 class="small font-weight-bold">In Late <span class="float-right" id="lateText">0%</span></h4>
                                    <div class="progress mb-4">
                                        <div id="lateBar" class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>

                                    <h4 class="small font-weight-bold">Present <span class="float-right" id="presentText">0%</span></h4>
                                    <div class="progress mb-4">
                                        <div id="presentBar" class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Donut Chart -->
                        <div class="col-xl-4 col-lg-5 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Attendance Chart</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Present</span>
                                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> In Late</span>
                                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Absent</span>
                                    </div>
                                    <div class="mt-2 text-center small" id="totalDays">Total: - hari</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <style>
                        .calendar-select {
                            padding: 5px 10px;
                            border-radius: 10px;
                            border: 1px solid #ddd;
                            background-color: #f8f9fc;
                            color: #4e73df;
                            font-weight: bold;
                            margin-left: 5px;
                            margin-right: 5px;
                        }

                        .calendar-select:focus {
                            outline: none;
                            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
                            border-color: #4e73df;
                        }

                        .calendar-toolbar {
                            display: flex;
                            align-items: center;
                            gap: 10px;
                        }

                        @media (max-width: 576px) {
                            .calendar-toolbar {
                                flex-direction: column;
                                align-items: flex-start;
                            }
                        }
                    </style>


                    <!-- Kalender + Tabel Kehadiran dalam 1 Baris -->
                    <div class="row">
                        <!-- Kalender Kehadiran -->
                        <div class="col-xl-5 col-lg-5 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Kalender Kehadiran</h6>
                                    <div class="calendar-toolbar">
                                        <select id="monthSelect" class="calendar-select"></select>
                                        <select id="yearSelect" class="calendar-select"></select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered text-center mb-0">
                                        <thead>
                                            <tr>
                                                <th>Minggu</th>
                                                <th>Senin</th>
                                                <th>Selasa</th>
                                                <th>Rabu</th>
                                                <th>Kamis</th>
                                                <th>Jumat</th>
                                                <th>Sabtu</th>
                                            </tr>
                                        </thead>
                                        <div class="mt-2 text-center medium">
                                            <span class="badge bg-success text-light">Present</span>
                                            <span class="badge bg-warning text-dark">In Late</span>
                                            <span class="badge bg-danger text-light">Absent</span>
                                        </div>

                                        <tbody id="calendarBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Data Kehadiran -->
                        <div class="col-xl-7 col-lg-7 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Kehadiran</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>NIM</th>
                                                    <th>PBL</th>
                                                    <th>Absen Hadir</th>
                                                    <th>Absen Pulang</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="absen-body">
                                                <tr>
                                                    <td colspan="6">Memuat data...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Simalas 2025</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" id="logout_button">Logout</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById("logout_button").addEventListener("click", function() {
                window.location.href = "logout.php";
            });
        </script>


        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="js/user/pie.js"></script>
        <script src="js/user/calendar.js"></script>

        <script src="js/user/proggresbar.js"></script>
        <script src="js/user/table.js"></script>
        <script src="js/demo/time.js"></script>
        <script src="js/generate_report.js"></script>

        <!-- jsPDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <!-- jsPDF AutoTable -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="vendor/chart.js/Chart.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="js/demo/chart-area-demo.js"></script>
        <script src="js/demo/chart-pie-demo.js"></script>
        <script src="js/demo/chart-bar-demo.js"></script>

        <!-- Page level plugins -->
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="js/demo/datatables-demo.js"></script>


</body>

</html>