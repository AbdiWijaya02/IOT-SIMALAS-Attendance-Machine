<?php
session_start();

$userid = $_SESSION["aiot_userid"];
$username = $_SESSION["aiot_nama"];
$userNIM = $_SESSION["aiot_NIM"];
$userPBL = $_SESSION["aiot_PBL"];
$useremail = $_SESSION["aiot_email"];

include("../classes/connect.php");
include("../classes/login.php");

if (isset($_SESSION["aiot_userid"]) && is_numeric($_SESSION["aiot_userid"])) {
    $id = $_SESSION["aiot_userid"];
    $login = new Login();

    $login->check_login($id);

    $result = $login->check_login($id);


    if ($result) {
    } else {

        header("Location: ../Login.php");
        die;
    }
} else {
    header("Location: ../Login.php");
    die;
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

    <title>Simalas - Dashboard</title>
    <link href="../assets/img/brail2.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <link href="../css/dropdown.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <img src="../assets/img/brail2.png" alt="custom icon" width="40" height="40">
                </div>
                <div class="sidebar-brand-text mx-3">Simalas <sup>2025</sup>
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>


            <!-- Nav Item - Tables -->
            <li class="nav-item active">
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-users"></i>
                    <span>Admin</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

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

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>



                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="../img/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="../img/undraw_profile_2.svg"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="../img/undraw_profile_3.svg"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($username); ?></span>
                                <img class="img-profile rounded-circle"
                                    src="../img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Kanan: Filter PBL + Filter Anggota -->
                    <div class="d-flex align-items-center gap-2">
                        <select id="pblFilter" style="margin-bottom: 15px;" onchange="filterByPBL()" class="form-control form-control-sm w-auto">
                            <option value="">Semua PBL</option>
                        </select>

                        <div id="anggotaContainer" style="display: none; margin-bottom: 15px;">

                            <select id="anggotaFilter" class="form-control form-control-sm w-auto">
                                <option value="">Pilih Anggota</option>
                            </select>
                        </div>

                        <!-- Setelah semua elemen chart dan select ditampilkan -->
                    </div>


                    <!-- Page Heading -->

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Earnings Overview -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4 h-100">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Grafik Kehadiran</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong> Rata-rata :</strong>
                                        <span id="avgHadirText">Loading...</span>
                                        <div class="mb-2 d-flex align-items-center justify-content-between">
                                            <strong>📊:</strong>
                                            <select id="metricSelect" class="form-control form-control-sm" style="width: auto;">
                                                <option value="jam_masuk">Rata-rata Jam Masuk</option>
                                                <option value="jam_pulang">Rata-rata Jam Pulang</option>
                                                <option value="durasi_jam">Durasi PBL</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>

                                    <script>
                                        fetch("../Adminbackend/rata_rata.php")
                                            .then(res => res.json())
                                            .then(data => {
                                                if (data.status === "success") {
                                                    document.getElementById("avgHadirText").textContent = data.rata_rata_hadir;
                                                } else {
                                                    document.getElementById("avgHadirText").textContent = "Gagal memuat";
                                                    console.error(data.message);
                                                }
                                            })
                                            .catch(err => {
                                                document.getElementById("avgHadirText").textContent = "Error";
                                                console.error("Fetch error:", err);
                                            });
                                    </script>
                                </div>

                            </div>
                        </div>

                        <!-- Kehadiran -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4 h-100">
                                <!-- Card Header -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Kehadiran</h6>
                                    <!-- Tambahkan Dropdown -->

                                </div>

                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Present</span>
                                        <span class="mr-2"><i class="fas fa-circle text-warning"></i> In Late</span>
                                        <span class="mr-2"><i class="fas fa-circle text-danger"></i> Absent</span>
                                    </div>
                                    <div id="pieInfo" class="text-center mt-2 text-muted font-weight-bold"></div>

                                </div>
                            </div>
                        </div>

                    </div>



                    <!-- Projects - moved to the right -->
                    <div class="row mt-4">
                        <!-- Bar Chart Example -->
                        <!-- Bar Chart Example -->
                        <div class="col-lg-8 mb-4 custom-dropdown-area">
                            <div class="card shadow h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        <span>Grafik Kehadiran Bulanan</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Filter 1: Tipe -->
                                        <select id="filterType" class="form-select form-select-sm">
                                            <option value="">Pilih Filter</option>
                                            <option value="PBL">PBL</option>
                                            <option value="Angkatan">Angkatan</option>
                                        </select>

                                        <!-- Filter 2: Value (PBL atau Angkatan) -->
                                        <select id="filterValue" class="form-select form-select-sm">
                                            <option value="">Semua</option>
                                        </select>

                                        <!-- Filter 3: Nama -->
                                        <select id="filterValue2" class="form-select form-select-sm">
                                            <option value="">Semua Nama</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <canvas id="myBarChart" width="100%" height="40"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Presentasi Kehadiran (Progress Bars) -->
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <!-- Kiri: Judul -->
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


                    </div>

                </div>

                <!-- /.container-fluid -->

            </div>


            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Simalas 2025

                        </span>
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
                    <a class="btn btn-primary" href="../logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- <canvas id="myPieChart"></canvas> -->




    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <!-- <script src="../js/demo/chart-area-demo.js"></script> -->
    <script src="../../js/demo/chart-bar-filter.js"></script>
    <script src="../js/demo/time.js"></script>



    <script>
        let pieChart, myChart = null,
            currentMetric = "jam_masuk";

        // PIE CHART
        function loadPieChart(pbl = '', nim = '') {
            fetch(`../Adminbackend/piechart.php?pbl=${pbl}&nim=${nim}`)
                .then(res => res.json())
                .then(data => {
                    const ctx = document.getElementById("myPieChart").getContext("2d");
                    if (pieChart) pieChart.destroy();

                    pieChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            // labels: ["Absent", "Late", "Present"],
                            datasets: [{
                                data: [data.absent, data.late, data.present],
                                backgroundColor: ['#e74a3b', '#f6c23e', '#1cc88a'],
                                hoverBackgroundColor: ['#c0392b', '#d4ac0d', '#17a673'],
                                hoverBorderColor: "rgba(234, 236, 244, 1)"
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: {
                                display: true
                            },
                            cutoutPercentage: 80,
                            tooltips: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                caretPadding: 10,
                            }
                        }
                    });

                    document.getElementById("pieInfo").textContent = `Total Hari Absensi: ${data.total}`;
                });
        }

        // PROGRESS BAR
        function loadProgressBar(pbl = '', nim = '') {
            const url = nim ? `../Adminbackend/proggresbar_data.php?nim=${nim}` : `../Adminbackend/proggresbar_data.php?pbl=${pbl}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    document.getElementById("absentText").textContent = `${data.absent}%`;
                    document.getElementById("lateText").textContent = `${data.late}%`;
                    document.getElementById("presentText").textContent = `${data.present}%`;

                    document.getElementById("absentBar").style.width = `${data.absent}%`;
                    document.getElementById("lateBar").style.width = `${data.late}%`;
                    document.getElementById("presentBar").style.width = `${data.present}%`;

                    document.getElementById("absentBar").setAttribute("aria-valuenow", data.absent);
                    document.getElementById("lateBar").setAttribute("aria-valuenow", data.late);
                    document.getElementById("presentBar").setAttribute("aria-valuenow", data.present);
                });
        }

        // AREA CHART (Rata-rata)
        function fetchChartData(metric, pbl = '', nim = '') {
            fetch(`../Adminbackend/rata_rata.php?metric=${metric}&pbl=${pbl}&nim=${nim}`)
                .then(res => res.json())
                .then(response => {
                    if (response.status !== "success") return;
                    const ctx = document.getElementById("myAreaChart").getContext("2d");
                    const labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                    const chartData = {
                        labels: labels,
                        datasets: [{
                            label: "Rata-rata " + formatLabel(metric),
                            data: response.data,
                            backgroundColor: "rgba(78, 115, 223, 0.05)",
                            borderColor: "rgba(78, 115, 223, 1)",
                            pointRadius: 3,
                            pointBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointBorderColor: "rgba(78, 115, 223, 1)",
                            pointHoverRadius: 3,
                            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                            pointHitRadius: 10,
                            pointBorderWidth: 2
                        }]
                    };

                    const chartOptions = {
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                left: 10,
                                right: 25,
                                top: 25,
                                bottom: 0
                            }
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    maxTicksLimit: 12
                                },
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    callback: val => val + " jam"
                                },
                                gridLines: {
                                    color: "rgb(234, 236, 244)",
                                    zeroLineColor: "rgb(234, 236, 244)",
                                    borderDash: [2],
                                    zeroLineBorderDash: [2]
                                }
                            }]
                        },
                        legend: {
                            display: false
                        },
                        tooltips: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            caretPadding: 10,
                            callbacks: {
                                label: t => "Rata-rata: " + t.yLabel + " jam"
                            }
                        }
                    };

                    if (myChart) {
                        myChart.data = chartData;
                        myChart.options = chartOptions;
                        myChart.update();
                    } else {
                        myChart = new Chart(ctx, {
                            type: 'line',
                            data: chartData,
                            options: chartOptions
                        });
                    }
                });
        }

        function formatLabel(metric) {
            return metric === "jam_masuk" ? "Jam Masuk" :
                metric === "jam_pulang" ? "Jam Pulang" :
                "Durasi Jam";
        }

        // ALL CHARTS
        function updateAllCharts(pbl = '', nim = '') {
            loadPieChart(pbl, nim);
            loadProgressBar(pbl, nim);
            fetchChartData(currentMetric, pbl, nim);
        }

        // INIT
        window.addEventListener("DOMContentLoaded", () => {
            const pblSelect = document.getElementById("pblFilter");
            const anggotaSelect = document.getElementById("anggotaFilter");
            const anggotaContainer = document.getElementById("anggotaContainer");

            // Load PBL Dropdown
            fetch("../Adminbackend/pbl_option.php")
                .then(res => res.json())
                .then(pbls => {
                    pbls.forEach(pbl => {
                        const opt = document.createElement("option");
                        opt.value = pbl;
                        opt.textContent = pbl;
                        pblSelect.appendChild(opt);
                    });
                    updateAllCharts();
                });

            // Change Metric (Dropdown)
            document.getElementById("metricSelect").addEventListener("change", function() {
                currentMetric = this.value;
                updateAllCharts(pblSelect.value, anggotaSelect.value);
            });

            // Change PBL
            pblSelect.addEventListener("change", () => {
                const pbl = pblSelect.value;
                if (!pbl) {
                    anggotaContainer.style.display = "none";
                    anggotaSelect.innerHTML = "";
                    updateAllCharts();
                    return;
                }

                fetch(`../Adminbackend/anggota_pbl.php?pbl=${pbl}`)
                    .then(res => res.json())
                    .then(anggotaList => {
                        anggotaSelect.innerHTML = "";
                        anggotaList.forEach(anggota => {
                            const opt = document.createElement("option");
                            opt.value = anggota.NIM;
                            opt.textContent = `${anggota.Nama} (${anggota.NIM})`;
                            anggotaSelect.appendChild(opt);
                        });
                        anggotaContainer.style.display = "block";
                        updateAllCharts(pbl);
                    });
            });

            // Change Anggota
            anggotaSelect.addEventListener("change", () => {
                updateAllCharts(pblSelect.value, anggotaSelect.value);
            });
        });
    </script>





</body>

</html>