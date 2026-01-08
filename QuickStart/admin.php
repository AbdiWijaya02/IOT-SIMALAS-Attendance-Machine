<?php
session_start();
include("classes/connect.php");
include("classes/login.php");

$db = new Database();
$conn = $db->connect();

// 1. Cek Login & Role Admin
if (isset($_SESSION["aiot_userid"]) && is_numeric($_SESSION["aiot_userid"])) {
    $id = $_SESSION["aiot_userid"];
    $login = new Login();
    $result = $login->check_login($id);

    if ($result) {
        // TAMBAHAN: Cek apakah role-nya 'admin'
        // Jika user.sql Anda menyimpan role di session saat login:
        if ($_SESSION["aiot_role"] !== 'admin') {
            header("Location: dashboard.php"); // Lempar user biasa ke dashboard
            exit();
        }

        $username = $_SESSION["aiot_nama"];
        $userNIM = $_SESSION["aiot_NIM"];
        $userPBL = $_SESSION["aiot_PBL"];

        // Mencegah cache
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");

        // 2. Ambil data PBL unik (DISTINCT) agar tidak double di dropdown
        $sqlPBL = "SELECT DISTINCT PBL FROM user WHERE PBL != ''";
        $rows = $db->read($sqlPBL);

        // 3. Ambil Status Loker
        $lockerStatus = [];
        $sqlLocker = "SELECT locker_number, PBL FROM locker_status";
        $resLocker = $conn->query($sqlLocker);
        if ($resLocker && $resLocker->num_rows > 0) {
            while ($row = $resLocker->fetch_assoc()) {
                $lockerStatus[$row['locker_number']] = $row['PBL'];
            }
        }
    } else {
        header("Location: login.php");
        exit();
    }
} else {
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

    <title>Simalas - Admin</title>
    <link href="assets/img/brail2.png" rel="icon">

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
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

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">


            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="Kehadiran.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Kehadiran</span></a>
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

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>

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
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg"
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
                                        <img class="rounded-circle" src="img/undraw_profile_2.svg"
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
                                        <img class="rounded-circle" src="img/undraw_profile_3.svg"
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
                                    src="img/undraw_profile.svg">
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
                    <!-- Page Heading & Report Button -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Tables</h1>
                        <a href="#" id="generateReport" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>


                    </div>




                    <!-- DataTales Example -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">DataTables </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Team</th>
                                            <th>Manager Project</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- <tfoot>
                                        <tr>
                                            <th>Team</th>
                                            <th>Manager Project</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </tfoot> -->
                                    <tbody>
                                        <tr>
                                            <td>SIMALAS</td>
                                            <td>Abdi Wijaya</td>
                                            <td>Esp32</td>
                                            <td>51</td>
                                            <td>Diambil</td>
                                            <td>2008/11/13</td>
                                        </tr>
                                        <tr>
                                            <td>SIMALAS</td>
                                            <td>Abdi Wijaya</td>
                                            <td>Arduino Atmega</td>
                                            <td>29</td>
                                            <td>Diloker</td>
                                            <td>2011/06/27</td>
                                        </tr>
                                        <tr>
                                            <td>SIMALAS</td>
                                            <td>Abdi Wijaya</td>
                                            <td>Arduino Atmega</td>
                                            <td>29</td>
                                            <td>Diloker</td>
                                            <td>2011/06/27</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-13 col-lg-13">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Locker</h6>
                            </div>
                            <div class="card-body">
                                <div class="locker-grid">
                                    <?php
                                    // Gunakan perulangan dari 1 sampai 4
                                    for ($i = 1; $i <= 4; $i++) {
                                        $lockerNum = $i; // Variabel ini akan berubah di setiap putaran (1, lalu 2, lalu 3, dst)
                                        $currentVal = isset($lockerStatus[$lockerNum]) ? $lockerStatus[$lockerNum] : "";
                                        
                                        // Tentukan warna background berdasarkan status
                                        $bgColor = ($currentVal == "Available") ? "#d4edda" : ($currentVal != "" ? "#f8d7da" : "#f0f0f0");
                                    ?>
                                        <div class="locker-box" style="background-color: <?php echo $bgColor; ?>;">
                                            <div class="locker-number"><?php echo $i; ?></div>
                                            
                                            <select class="locker-select" data-locker="<?php echo $i; ?>" onchange="updateColor(this)">
                                                <?php
                                                echo '<option value="">Pilih</option>';
                                                echo '<option value="Available"' . ($currentVal == "Available" ? " selected" : "") . '>Available</option>';

                                                // Menampilkan opsi PBL dari database
                                                if ($rows) {
                                                    foreach ($rows as $row) {
                                                        $val = htmlspecialchars($row['PBL']);
                                                        $selected = ($val == $currentVal) ? " selected" : "";
                                                        echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    <?php 
                                    } // Akhir dari pengulangan for
                                    ?>
                                </div>

                                    <div class="locker-box">
                                        <div class="locker-number">2</div>
                                        <select class="locker-select" data-locker="2" onchange="updateColor(this)">
                                            <?php
                                            $lockerNum = 2;
                                            $currentVal = isset($lockerStatus[$lockerNum]) ? $lockerStatus[$lockerNum] : "";

                                            echo '<option value="">Pilih</option>';
                                            echo '<option value="Available"' . ($currentVal == "Available" ? " selected" : "") . '>Available</option>';

                                            foreach ($rows as $row) {
                                                $val = htmlspecialchars($row['PBL']);
                                                $selected = ($val == $currentVal) ? " selected" : "";
                                                echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="locker-box">
                                        <div class="locker-number">3</div>
                                        <select class="locker-select" data-locker="3" onchange="updateColor(this)">
                                            <?php
                                            $lockerNum = 3;
                                            $currentVal = isset($lockerStatus[$lockerNum]) ? $lockerStatus[$lockerNum] : "";

                                            echo '<option value="">Pilih</option>';
                                            echo '<option value="Available"' . ($currentVal == "Available" ? " selected" : "") . '>Available</option>';

                                            foreach ($rows as $row) {
                                                $val = htmlspecialchars($row['PBL']);
                                                $selected = ($val == $currentVal) ? " selected" : "";
                                                echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="locker-box">
                                        <div class="locker-number">4</div>
                                        <select class="locker-select" data-locker="4" onchange="updateColor(this)">
                                            <?php
                                            $lockerNum = 4;
                                            $currentVal = isset($lockerStatus[$lockerNum]) ? $lockerStatus[$lockerNum] : "";

                                            echo '<option value="">Pilih</option>';
                                            echo '<option value="Available"' . ($currentVal == "Available" ? " selected" : "") . '>Available</option>';

                                            foreach ($rows as $row) {
                                                $val = htmlspecialchars($row['PBL']);
                                                $selected = ($val == $currentVal) ? " selected" : "";
                                                echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <style>
                        .locker-grid {
                            display: grid;
                            grid-template-columns: repeat(4, 1fr);
                            gap: 6px;
                            padding: 6px;
                        }

                        .locker-box {
                            width: 100%;
                            padding-top: 60%;
                            background-color: #f0f0f0;
                            border: 2px solid #007bff;
                            position: relative;
                            border-radius: 6px;
                            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
                            overflow: hidden;
                            transition: background-color 0.3s ease;
                        }

                        .locker-number {
                            position: absolute;
                            top: 10%;
                            left: 50%;
                            transform: translateX(-50%);
                            font-weight: bold;
                            color: #007bff;
                            font-size: 1rem;
                        }

                        .locker-select {
                            position: absolute;
                            bottom: 10%;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 80%;
                            padding: 5px;
                            font-size: 0.9rem;
                            border-radius: 5px;
                            border: 1px solid #ccc;
                        }
                    </style>


                    <script>
                        let selectedPBL = "";

                        function updateColor(selectElement, triggerModal = true) {
                            const box = selectElement.closest('.locker-box');
                            const value = selectElement.value;

                            if (value === 'Available') {
                                box.style.backgroundColor = '#d4edda';
                                const lockerNumber = selectElement.getAttribute("data-locker");
                                const data = [{
                                    locker: lockerNumber,
                                    pbl: value
                                }];

                                const xhr = new XMLHttpRequest();
                                xhr.open("POST", "classes/set_loker.php", true);
                                xhr.setRequestHeader("Content-type", "application/json");

                                xhr.onload = function() {
                                    console.log("Status updated to Available.");
                                };

                                xhr.onerror = function() {
                                    console.log("Gagal kirim data ke server.");
                                };

                                xhr.send(JSON.stringify(data));
                            } else if (value !== '') {
                                box.style.backgroundColor = '#f8d7da';
                                if (triggerModal) {
                                    // PAKAI JQUERY MODAL BOOTSTRAP 4
                                    $('#setupModal').modal('show');
                                }
                            } else {
                                box.style.backgroundColor = '#f0f0f0';
                            }
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelectorAll('.locker-select').forEach(select => updateColor(select, false));

                            document.getElementById("setup_button").addEventListener("click", async function() {
                                const selects = document.querySelectorAll(".locker-select");
                                const data = Array.from(selects).map(select => ({
                                    locker: select.getAttribute("data-locker"),
                                    pbl: select.value
                                }));

                                try {
                                    const response = await fetch("classes/set_loker.php", {
                                        method: "POST",
                                        headers: {
                                            "Content-Type": "application/json"
                                        },
                                        body: JSON.stringify(data)
                                    });
                                    const result = await response.text();
                                    alert(result);
                                } catch (error) {
                                    alert("Gagal kirim data ke server.");
                                } finally {
                                    // TUTUP MODAL PAKAI JQUERY
                                    $('#setupModal').modal('hide');
                                }
                            });
                        });
                    </script>
                </div>
            </div>

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

    <!-- Setup Modal-->
    <div class="modal fade" id="setupModal" tabindex="-1" role="dialog" aria-labelledby="setupModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setupModalLabel">System Setup</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Select "Set Up" to proceed with the system configuration.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="setup_button">Set Up</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("setup_button").addEventListener("click", async function() {
            const selects = document.querySelectorAll(".locker-select");
            const data = Array.from(selects).map(select => ({
                locker: select.getAttribute("data-locker"),
                pbl: select.value
            }));

            try {
                const response = await fetch("classes/set_loker.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.text();
                alert(result);
                $('#setupModal').modal('hide'); // Langsung tutup modal setelah alert
            } catch (error) {
                alert("Gagal kirim data ke server.");
            }
        });
    </script>


    <script>
        function updateTime() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();

            // Menambahkan '0' jika nilainya kurang dari 10
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            var timeString = hours + ':' + minutes + ':' + seconds;
            document.getElementById('timeDisplay').textContent = timeString;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch_time.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    var response = JSON.parse(xhr.responseText);
                    console.log(response);
                }
            };
            xhr.send("client_time=" + timeString);


        }
        setInterval(updateTime, 1000);
        setInterval(sendTimeToServer, 1000);
    </script>

    <script>
        document.getElementById("generateReport").addEventListener("click", () => {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape'); // Landscape

            doc.text("Laporan Data Barang", 14, 15);

            // Ambil tabel dan ubah jadi PDF
            doc.autoTable({
                html: '#dataTable',
                startY: 20,
                styles: {
                    fontSize: 10,
                    cellPadding: 3
                },
                headStyles: {
                    fillColor: [41, 128, 185]
                }
            });

            doc.save("laporan_barang.pdf");
        });
    </script>





    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>