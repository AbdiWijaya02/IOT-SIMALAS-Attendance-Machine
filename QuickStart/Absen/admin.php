<?php
session_start();
include("../classes/connect.php");
include("../classes/login.php");

$db = new Database();
$conn = $db->connect();

// 1. Cek Login & Otoritas Admin (LOGIKA LAMA DIPERTAHANKAN)
if (isset($_SESSION["aiot_userid"]) && is_numeric($_SESSION["aiot_userid"])) {
    $id = $_SESSION["aiot_userid"];
    $login = new Login();
    $result = $login->check_login($id);

    if ($result) {
        // Pengecekan role
        if ($_SESSION["aiot_role"] !== 'admin') {
            header("Location: dashboard.php");
            exit();
        }

        $username = $_SESSION["aiot_nama"];
        
        // CSRF Token (Tambahan Security Recommended)
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrf_token = $_SESSION['csrf_token'];
        
        // --- DATA FETCHING (Disesuaikan Requirements) ---

        // A. Daftar PBL (Untuk Dropdown Hak Akses)
        $sqlPBL = "SELECT DISTINCT PBL FROM user WHERE PBL != ''";
        $rowsPBL = $db->read($sqlPBL);

        // B. Hak Akses Loker (Tabel locker_status) - Siapa yang BERHAK
        $lockerHak = [];
        $sqlLock = "SELECT locker_number, PBL FROM locker_status";
        $resLock = $conn->query($sqlLock);
        if ($resLock && $resLock->num_rows > 0) {
            while ($r = $resLock->fetch_assoc()) {
                $lockerHak[$r['locker_number']] = $r['PBL'];
            }
        }

        // C. Status Fisik Barang (Tabel log_loker) - Apa yang TERJADI (IN/OUT)
        // Logika: Ambil log terakhir per loker untuk tentukan warna
        $lockerStatusFisik = [];
        $sqlLastLog = "
            SELECT l.locker_number, l.status, l.source 
            FROM log_loker l
            INNER JOIN (
                SELECT locker_number, MAX(id) as max_id 
                FROM log_loker 
                GROUP BY locker_number
            ) latest ON l.locker_number = latest.locker_number AND l.id = latest.max_id
        ";
        $resStatus = $conn->query($sqlLastLog);
        if ($resStatus) {
            while ($r = $resStatus->fetch_assoc()) {
                $lockerStatusFisik[$r['locker_number']] = $r;
            }
        }

        // D. Data Log untuk Tabel (Menggantikan Dummy Data)
        $tableLogs = [];
        $sqlTable = "
            SELECT ll.*, u.nama as user_nama, u.PBL as user_pbl 
            FROM log_loker ll 
            LEFT JOIN user u ON ll.user_id = u.user_id 
            ORDER BY ll.id DESC LIMIT 100
        ";
        $resTable = $conn->query($sqlTable);
        if ($resTable) {
            while ($r = $resTable->fetch_assoc()) {
                $tableLogs[] = $r;
            }
        }

    } else {
        header("Location: ../login.php"); exit();
    }
} else {
    header("Location: ../login.php"); exit();
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
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <style>
        /* Style Tambahan untuk Grid Loker Baru */
        .locker-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            padding: 10px;
        }
        .locker-box {
            width: 100%;
            /* Padding top removed to use flex layout inside */
            min-height: 250px; 
            background-color: #f0f0f0;
            border: 2px solid #007bff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            transition: background-color 0.3s ease;
        }
        .locker-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #4e73df;
            margin-bottom: 5px;
        }
        .locker-status-text {
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            background: rgba(255,255,255,0.7);
            padding: 2px 8px;
            border-radius: 4px;
        }
        .locker-select {
            width: 100%;
            padding: 5px;
            font-size: 0.85rem;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .admin-controls {
            display: flex;
            gap: 5px;
            width: 100%;
        }
        .admin-controls .btn {
            flex: 1;
            font-size: 0.7rem;
            padding: 4px;
        }
        
        /* Responsive for small screens */
        @media (max-width: 768px) {
            .locker-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) {
            .locker-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar (KODE LAMA TETAP) -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <img src="../assets/img/brail2.png" alt="custom icon" width="40" height="40">
                </div>
                <div class="sidebar-brand-text mx-3">Simalas <sup>2025</sup></div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="../admin.php">
                    <i class="fas fa-users"></i>
                    <span>Admin</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar (KODE LAMA TETAP) -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Time Display -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="#" id="timeDisplay" style="
                            border: 2px solid #f4f4f4; border-radius: 12px; padding: 12px 20px;
                            font-size: 18px; color: rgb(15, 15, 15); background-color: rgb(#f4f4f4);
                            transition: all 0.3s ease;" 
                            onmouseover="this.style.backgroundColor='#e6f0ff'" 
                            onmouseout="this.style.backgroundColor='#f0f8ff'">
                                <i class="fas fa-clock me-2"></i>
                                <span id="timeText">TIME</span>
                            </a>
                        </li>
                    </ul>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($username); ?></span>
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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
                        <h1 class="h3 mb-0 text-gray-800">Monitoring & Log Tables</h1>
                        <a href="#" id="generateReport" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>
                    </div>

                    <!-- LOCKER MONITORING GRID (MODIFIKASI: LOGIKA STATUS BARANG + ADMIN CONTROL) -->
                    <div class="col-xl-13 col-lg-13 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Status Loker Real-time (Fisik) & Hak Akses</h6>
                            </div>
                            <div class="card-body">
                                <div class="locker-grid">
                                    <?php
                                    for ($i = 1; $i <= 4; $i++) {
                                        // 1. Tentukan Hak Akses (Siapa Pemiliknya) - dari tabel locker_status
                                        $hakPBL = isset($lockerHak[$i]) ? $lockerHak[$i] : "";

                                        // 2. Tentukan Status Fisik (Warna) - dari tabel log_loker terakhir
                                        $lastLog = isset($lockerStatusFisik[$i]) ? $lockerStatusFisik[$i] : null;
                                        $statusFisik = $lastLog ? $lastLog['status'] : 'UNKNOWN';

                                        // Logika Warna
                                        // IN = Merah (Ada barang)
                                        // OUT = Hijau (Kosong)
                                        // UNKNOWN = Abu-abu
                                        $bgColor = "#f0f0f0"; // Default
                                        $statusLabel = "No Data";
                                        
                                        if ($statusFisik === 'IN') {
                                            $bgColor = "#f8d7da"; // Merah muda
                                            $statusLabel = "TERISI (IN)";
                                        } elseif ($statusFisik === 'OUT') {
                                            $bgColor = "#d4edda"; // Hijau muda
                                            $statusLabel = "KOSONG (OUT)";
                                        }
                                    ?>
                                        <div class="locker-box" style="background-color: <?php echo $bgColor; ?>;">
                                            <div class="locker-number">LOKER <?php echo $i; ?></div>
                                            
                                            <!-- Label Status Visual -->
                                            <div class="locker-status-text">
                                                Status: <?php echo $statusLabel; ?><br>
                                                <small>Last: <?php echo $lastLog ? $lastLog['source'] : '-'; ?></small>
                                            </div>

                                            <!-- Dropdown Hak Akses (PBL) -->
                                            <div style="width:100%">
                                                <label style="font-size:0.7rem; font-weight:bold; margin-bottom:0;">Hak Akses:</label>
                                                <select class="locker-select" data-locker="<?php echo $i; ?>">
                                                    <option value="">-- Unassigned --</option>
                                                    <?php
                                                    if ($rowsPBL) {
                                                        foreach ($rowsPBL as $row) {
                                                            $val = htmlspecialchars($row['PBL']);
                                                            $selected = ($val == $hakPBL) ? " selected" : "";
                                                            echo '<option value="' . $val . '"' . $selected . '>' . $val . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <!-- Tombol Manual Admin (Requirement Baru) -->
                                            <div class="admin-controls">
                                                <button class="btn btn-danger btn-admin-action" data-locker="<?php echo $i; ?>" data-action="IN">
                                                    Set ADA
                                                </button>
                                                <button class="btn btn-success btn-admin-action" data-locker="<?php echo $i; ?>" data-action="OUT">
                                                    Reset KOSONG
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="mt-2 text-center small text-muted">
                                    <i class="fas fa-info-circle"></i> Tombol "Set ADA/KOSONG" akan mencatat log baru dengan source 'admin'. History lama tidak dihapus.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL HISTORI LOG (MODIFIKASI: DATA DARI DB, BUKAN DUMMY) -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Log Aktivitas</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Tim / PBL</th>
                                            <th>User Name</th>
                                            <th>Loker</th>
                                            <th>Event</th>
                                            <th>Waktu</th>
                                            <th>Durasi (dtk)</th>
                                            <th>Source</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if(!empty($tableLogs)) {
                                            foreach ($tableLogs as $log): 
                                                // Tentukan waktu tampil (keluar atau masuk)
                                                $tampilWaktu = ($log['status'] == 'OUT') ? $log['waktu_keluar'] : $log['waktu_masuk'];
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($log['user_pbl'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($log['user_nama'] ?? 'Unknown'); ?></td>
                                            <td><?php echo $log['locker_number']; ?></td>
                                            <td>
                                                <?php if($log['status']=='IN') echo '<span class="text-danger font-weight-bold">IN</span>'; 
                                                      else echo '<span class="text-success font-weight-bold">OUT</span>'; ?>
                                            </td>
                                            <td><?php echo $tampilWaktu; ?></td>
                                            <td><?php echo $log['durasi_detik'] ?? '-'; ?></td>
                                            <td><?php echo htmlspecialchars($log['source']); ?></td>
                                        </tr>
                                        <?php 
                                            endforeach; 
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>Belum ada data log aktivitas.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer (KODE LAMA TETAP) -->
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

    <!-- Logout Modal (KODE LAMA TETAP)-->
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

    <!-- Scripts (KODE LAMA TETAP + LOGIKA BARU DI DALAMNYA) -->
    
    <!-- Core & Plugins -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
    
    <!-- PDF & Time Scripts (Bawaan Anda) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="../js/demo/generate_pdf.js"></script>
    <script src="../js/demo/time.js"></script>

    <!-- Page Specific Script -->
    <script src="../js/demo/datatables-demo.js"></script>

    <script>
        // Logika Logout
        document.getElementById("logout_button").addEventListener("click", function() {
            window.location.href = "../logout.php";
        });

        const CSRF_TOKEN = "<?php echo $csrf_token; ?>";

        // 1. Logika Update Hak Akses (Dropdown)
        // Menggunakan AJAX Fetch API (Lebih modern) atau jQuery Ajax
        $('.locker-select').change(function() {
            const locker = $(this).data('locker');
            const pbl = $(this).val();

            // Panggil API update_hak.php (Anda perlu membuat file ini di folder ../classes/)
            // Logikanya hanya update tabel locker_status
            fetch("../classes/update_hak.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ locker: locker, pbl: pbl, csrf: CSRF_TOKEN })
            })
            .then(response => response.text())
            .then(data => {
                // Opsional: Tampilkan notifikasi kecil / console log
                console.log("Hak akses updated: " + data);
            })
            .catch(error => alert("Gagal update hak akses: " + error));
        });

        // 2. Logika Admin Manual Action (Tombol IN/OUT)
        $('.btn-admin-action').click(function() {
            const locker = $(this).data('locker');
            const action = $(this).data('action'); // 'IN' atau 'OUT'
            
            const konfirmasi = confirm(`Apakah Anda yakin mengubah status loker ${locker} menjadi ${action} secara manual?`);
            if(!konfirmasi) return;

            // Panggil API manual_log.php (Anda perlu membuat file ini di folder ../classes/)
            // Logikanya: INSERT INTO log_loker (..., source='admin')
            fetch("../classes/manual_log.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ locker: locker, action: action, csrf: CSRF_TOKEN })
            })
            .then(response => response.text())
            .then(data => {
                if(data.trim() === "Success") {
                    location.reload(); // Reload halaman untuk melihat perubahan warna & tabel
                } else {
                    alert("Gagal update status: " + data);
                }
            })
            .catch(error => alert("Error koneksi: " + error));
        });

        // Setup Modal Button (Logika Lama Dipertahankan untuk Setup Massal jika masih dibutuhkan)
        // ... (Kode setup modal lama Anda bisa tetap ditaruh di sini jika fitur itu masih aktif)
    </script>

</body>
</html>
