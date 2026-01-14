<?php
session_start();

// --- [BAGIAN 1: AUTHENTICATION] ---
$userid = $_SESSION["aiot_userid"] ?? null;
$username = $_SESSION["aiot_nama"] ?? null;

include("../classes/connect.php");
include("../classes/login.php");

if (isset($_SESSION["aiot_userid"]) && is_numeric($_SESSION["aiot_userid"])) {
    $id = $_SESSION["aiot_userid"];
    $login = new Login();
    $login->check_login($id);
    if (!$login->check_login($id)) {
        header("Location: ../Login.php");
        die;
    }
} else {
    header("Location: ../Login.php");
    die;
}

// --- [BAGIAN 2: LOGIKA DASHBOARD & EXPORT] ---
$conn_dashboard = mysqli_connect("localhost", "root", "", "aiot"); 

// Inisialisasi Filter
$filter_type = $_GET['filter_type'] ?? 'monthly';
$start_date = $_GET['start_date'] ?? date('Y-m');
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$filter_angkatan = $_GET['angkatan'] ?? 'all';
$filter_pbl = $_GET['pbl'] ?? 'all';
$filter_user = $_GET['user'] ?? 'all';

// Bangun WHERE Clause (Digunakan untuk Tampilan & Export)
$where_cond = ["1=1"];

if ($filter_type == 'daily') {
    $tgl = $start_date ?: date('Y-m-d');
    $where_cond[] = "a.tanggal = '$tgl'";
    $periode_label = "Tanggal: " . date('d M Y', strtotime($tgl));
    $filename_suffix = "Harian_" . str_replace('-', '', $tgl);
} elseif ($filter_type == 'range') {
    $s = $start_date ?: date('Y-m-01');
    $e = $end_date ?: date('Y-m-d');
    $where_cond[] = "a.tanggal BETWEEN '$s' AND '$e'";
    $periode_label = "Periode: " . date('d/m/y', strtotime($s)) . " - " . date('d/m/y', strtotime($e));
    $filename_suffix = "Range_" . str_replace('-', '', $s) . "_to_" . str_replace('-', '', $e);
} else { 
    $bln = $start_date ?: date('Y-m');
    $where_cond[] = "DATE_FORMAT(a.tanggal, '%Y-%m') = '$bln'";
    $periode_label = "Bulan: " . date('F Y', strtotime($bln . "-01"));
    $filename_suffix = "Bulanan_" . str_replace('-', '', $bln);
}

if ($filter_angkatan != 'all') $where_cond[] = "a.NIM IN (SELECT NIM FROM user WHERE Angkatan = '$filter_angkatan')";
if ($filter_pbl != 'all') $where_cond[] = "a.PBL = '$filter_pbl'";
if ($filter_user != 'all') $where_cond[] = "a.NIM = '$filter_user'";

$where_sql = implode(" AND ", $where_cond);

// --- [FITUR EXPORT CSV] ---
// Cek apakah tombol "Download CSV" ditekan
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Laporan_Kehadiran_' . $filename_suffix . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Header Kolom CSV
    fputcsv($output, array('No', 'Tanggal', 'Nama', 'NIM', 'PBL', 'Jam Masuk', 'Jam Pulang', 'Keterangan Masuk', 'Status'));
    
    // Query Data untuk CSV
    $q_export = mysqli_query($conn_dashboard, "SELECT * FROM absen a WHERE $where_sql ORDER BY a.tanggal DESC, a.absen_hadir ASC");
    
    $no = 1;
    while ($row = mysqli_fetch_assoc($q_export)) {
        $jam_masuk = (!empty($row['absen_hadir'])) ? date('H:i', strtotime($row['absen_hadir'])) : '-';
        $jam_pulang = (!empty($row['absen_pulang'])) ? date('H:i', strtotime($row['absen_pulang'])) : '-';
        $status_masuk = $row['status_masuk'] ?? '-';
        
        fputcsv($output, array(
            $no++,
            date('d/m/Y', strtotime($row['tanggal'])),
            $row['Nama'],
            "'" . $row['NIM'], // Tanda kutip agar Excel membacanya sebagai teks (bukan angka eksponensial)
            $row['PBL'],
            $jam_masuk,
            $jam_pulang,
            $status_masuk,
            $row['status_kehadiran']
        ));
    }
    
    fclose($output);
    exit; // Hentikan script agar tidak me-render HTML di bawahnya
}

// --- [QUERY UNTUK DASHBOARD] ---

// 1. Stats & Pie Chart
$q_sum = mysqli_query($conn_dashboard, "SELECT status_kehadiran, COUNT(*) as cnt FROM absen a WHERE $where_sql GROUP BY status_kehadiran");
$stats = ['Hadir' => 0, 'Tidak Hadir' => 0];
while($r = mysqli_fetch_assoc($q_sum)) {
    $status = trim($r['status_kehadiran']);
    if(stripos($status, 'Hadir') !== false && stripos($status, 'Tidak') === false) {
        $stats['Hadir'] += $r['cnt'];
    } else {
        $stats['Tidak Hadir'] += $r['cnt'];
    }
}

// 2. Bar Chart
$q_trend = mysqli_query($conn_dashboard, "SELECT a.tanggal, 
    SUM(CASE WHEN status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) as h,
    SUM(CASE WHEN status_kehadiran != 'Hadir' THEN 1 ELSE 0 END) as th
    FROM absen a WHERE $where_sql GROUP BY a.tanggal ORDER BY a.tanggal ASC");
$trend_labels = []; $trend_hadir = []; $trend_tidak = [];
while($r = mysqli_fetch_assoc($q_trend)) {
    $trend_labels[] = date('d/m', strtotime($r['tanggal']));
    $trend_hadir[] = $r['h'];
    $trend_tidak[] = $r['th'];
}

// 3. Tabel Data
$q_table = mysqli_query($conn_dashboard, "SELECT * FROM absen a WHERE $where_sql ORDER BY a.tanggal DESC, a.absen_hadir ASC");

// Dropdown Options
$dd_angkatan = mysqli_query($conn_dashboard, "SELECT DISTINCT Angkatan FROM user WHERE Angkatan != '' ORDER BY Angkatan DESC");
$dd_pbl = mysqli_query($conn_dashboard, "SELECT DISTINCT nama_pbl FROM pbl ORDER BY nama_pbl ASC");
$dd_user = mysqli_query($conn_dashboard, "SELECT NIM, Nama FROM user WHERE role='user' ORDER BY Nama ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Simalas - Dashboard</title>
    
    <link href="../assets/img/brail2.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../css/dropdown.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15"><img src="../assets/img/brail2.png" alt="custom icon" width="40" height="40"></div>
                <div class="sidebar-brand-text mx-3">Simalas <sup>2025</sup></div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active"><a class="nav-link" href="dashboard.php"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li class="nav-item active"><a class="nav-link" href="admin.php"><i class="fas fa-users"></i><span>Admin</span></a></li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline"><button class="rounded-circle border-0" id="sidebarToggle"></button></div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="#" id="timeDisplay" style="border: 2px solid #f4f4f4; border-radius: 12px; padding: 12px 20px; font-size: 18px; color: rgb(15, 15, 15); background-color: rgb(#f4f4f4);">
                                <i class="fas fa-clock me-2"></i><span id="timeText">TIME</span>
                            </a>
                        </li>
                    </ul>
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($username) ?></span>
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- KONTEN -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard Monitoring</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-calendar fa-sm text-white-50"></i> <?= $periode_label ?></a>
                    </div>

                    <!-- 1. FORM FILTER + EXPORT -->
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Filter Data</h6></div>
                        <div class="card-body bg-light">
                            <form method="GET" action="">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="small font-weight-bold">Tipe Filter:</label>
                                        <select name="filter_type" id="filter_type" class="form-control form-control-sm" onchange="toggleDateInputs()">
                                            <option value="monthly" <?= $filter_type == 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                                            <option value="range" <?= $filter_type == 'range' ? 'selected' : '' ?>>Rentang Tanggal</option>
                                            <option value="daily" <?= $filter_type == 'daily' ? 'selected' : '' ?>>Harian</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="small font-weight-bold" id="date-label">Waktu:</label>
                                        <input type="<?= $filter_type == 'monthly' ? 'month' : 'date' ?>" name="start_date" id="start_date" class="form-control form-control-sm" value="<?= $start_date ?>">
                                    </div>
                                    <div class="col-md-3 mb-3" id="end-date-box" style="display: <?= $filter_type == 'range' ? 'block' : 'none' ?>">
                                        <label class="small font-weight-bold">Sampai:</label>
                                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end_date ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3"><select name="angkatan" class="form-control form-control-sm mb-2"><option value="all">Semua Angkatan</option><?php while($r = mysqli_fetch_assoc($dd_angkatan)): ?><option value="<?= $r['Angkatan'] ?>" <?= $filter_angkatan == $r['Angkatan'] ? 'selected' : '' ?>><?= $r['Angkatan'] ?></option><?php endwhile; ?></select></div>
                                    <div class="col-md-3"><select name="pbl" class="form-control form-control-sm mb-2"><option value="all">Semua PBL</option><?php while($r = mysqli_fetch_assoc($dd_pbl)): ?><option value="<?= $r['nama_pbl'] ?>" <?= $filter_pbl == $r['nama_pbl'] ? 'selected' : '' ?>><?= $r['nama_pbl'] ?></option><?php endwhile; ?></select></div>
                                    <div class="col-md-3"><select name="user" class="form-control form-control-sm mb-2"><option value="all">Semua Mahasiswa</option><?php while($r = mysqli_fetch_assoc($dd_user)): ?><option value="<?= $r['NIM'] ?>" <?= $filter_user == $r['NIM'] ? 'selected' : '' ?>><?= $r['Nama'] ?></option><?php endwhile; ?></select></div>
                                    
                                    <!-- TOMBOL ACTION (Cari & Export) -->
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-6 pr-1">
                                                <button type="submit" class="btn btn-primary btn-sm btn-block" title="Tampilkan Data"><i class="fas fa-search"></i> Cari</button>
                                            </div>
                                            <div class="col-6 pl-1">
                                                <button type="submit" name="export" value="csv" class="btn btn-success btn-sm btn-block" title="Download Excel/CSV"><i class="fas fa-download"></i> CSV</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 2. STATS & GRAPH -->
                    <div class="row">
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Hadir</div><div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['Hadir'] ?></div></div>
                                        <div class="col-auto"><i class="fas fa-check fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2"><div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tidak Hadir</div><div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['Tidak Hadir'] ?></div></div>
                                        <div class="col-auto"><i class="fas fa-times fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Tren Kehadiran</h6></div>
                                <div class="card-body"><div class="chart-area" style="height: 300px;"><canvas id="myTrendChart"></canvas></div></div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Proporsi</h6></div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2" style="height: 250px;"><canvas id="myProporsiChart"></canvas></div>
                                    <div class="mt-4 text-center small"><span class="mr-2"><i class="fas fa-circle text-success"></i> Hadir</span><span class="mr-2"><i class="fas fa-circle text-danger"></i> Tidak Hadir</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. TABEL DATA -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Detail Data Kehadiran</h6></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama</th>
                                            <th>PBL</th>
                                            <th>Masuk</th>
                                            <th>Pulang</th>
                                            <th>Ket. Masuk</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; if (mysqli_num_rows($q_table) > 0): while($row = mysqli_fetch_assoc($q_table)): 
                                            $jam_masuk = (!empty($row['absen_hadir'])) ? date('H:i', strtotime($row['absen_hadir'])) : '-';
                                            $jam_pulang = (!empty($row['absen_pulang'])) ? date('H:i', strtotime($row['absen_pulang'])) : '-';
                                            $bg_status = ($row['status_kehadiran'] == 'Hadir') ? 'badge-success' : 'badge-danger';
                                            $status_masuk = $row['status_masuk'] ?? '-';
                                            $bg_masuk = (stripos($status_masuk, 'Tepat') !== false) ? 'badge-info' : ((stripos($status_masuk, 'Lambat') !== false) ? 'badge-warning' : 'badge-secondary');
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                            <td><?= htmlspecialchars($row['Nama']) ?><br><small class="text-muted"><?= htmlspecialchars($row['NIM']) ?></small></td>
                                            <td><?= htmlspecialchars($row['PBL']) ?></td>
                                            <td><?= $jam_masuk ?></td>
                                            <td><?= $jam_pulang ?></td>
                                            <td><span class="badge <?= $bg_masuk ?>"><?= $status_masuk ?></span></td>
                                            <td><span class="badge <?= $bg_status ?>"><?= $row['status_kehadiran'] ?></span></td>
                                        </tr>
                                        <?php endwhile; else: ?>
                                        <tr><td colspan="8" class="text-center">Tidak ada data.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <footer class="sticky-footer bg-white"><div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; Simalas 2025</span></div></div></footer>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="../js/sb-admin-2.min.js"></script>
    <script src="../js/demo/time.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() { $('#dataTable').DataTable(); });
        function toggleDateInputs() {
            const type = document.getElementById('filter_type').value;
            const input = document.getElementById('start_date');
            const endBox = document.getElementById('end-date-box');
            if (type === 'monthly') { input.type = 'month'; endBox.style.display = 'none'; } 
            else if (type === 'range') { input.type = 'date'; endBox.style.display = 'block'; } 
            else { input.type = 'date'; endBox.style.display = 'none'; }
        }
        window.onload = toggleDateInputs;

        new Chart(document.getElementById("myTrendChart"), {
            type: 'bar',
            data: {
                labels: <?= json_encode($trend_labels) ?>,
                datasets: [{ label: "Hadir", backgroundColor: "#1cc88a", data: <?= json_encode($trend_hadir) ?> }, { label: "Tidak Hadir", backgroundColor: "#e74a3b", data: <?= json_encode($trend_tidak) ?> }]
            },
            options: { maintainAspectRatio: false, scales: { xAxes: [{ gridLines: { display: false } }], yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] }, legend: { position: 'bottom' } }
        });

        new Chart(document.getElementById("myProporsiChart"), {
            type: 'doughnut',
            data: {
                labels: ["Hadir", "Tidak Hadir"],
                datasets: [{ data: [<?= $stats['Hadir'] ?>, <?= $stats['Tidak Hadir'] ?>], backgroundColor: ['#1cc88a', '#e74a3b'] }]
            },
            options: { maintainAspectRatio: false, cutoutPercentage: 70, legend: { display: false } }
        });
    </script>
</body>
</html>
