<?php
session_start();
include("../classes/connect.php");
include("../classes/login.php");


$db = new Database();
$conn = $db->connect();


// 1. Cek Login & Otoritas Admin
if (isset($_SESSION["aiot_userid"]) && is_numeric($_SESSION["aiot_userid"])) {
    $id = $_SESSION["aiot_userid"];
    $login = new Login();
    $result = $login->check_login($id);


    if ($result) {
        if ($_SESSION["aiot_role"] !== 'admin') {
            header("Location: dashboard.php");
            exit();
        }


        $username = $_SESSION["aiot_nama"];

        // Menentukan halaman yang aktif (default: dashboard)
        $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';


        // ========================================
        // DATA UNTUK HALAMAN DASHBOARD (Loker)
        // ========================================
        if ($page == 'dashboard') {

            // A. Ambil daftar PBL UNIK
            $sqlPBL = "SELECT DISTINCT PBL FROM user WHERE PBL != '' ORDER BY PBL ASC";
            $rowsPBL = $db->read($sqlPBL);


            // B. Ambil Hak Akses Loker
            $lockerHak = [];
            $sqlLock = "SELECT locker_number, PBL FROM locker_status";
            $resLock = $conn->query($sqlLock);
            if ($resLock && $resLock->num_rows > 0) {
                while ($r = $resLock->fetch_assoc()) {
                    $lockerHak[$r['locker_number']] = $r['PBL'];
                }
            }


            // C. Ambil Status Fisik Barang Terakhir
            $lockerFisik = [];
            $sqlStatus = "
                SELECT l.locker_number, l.status, l.source 
                FROM log_loker l
                INNER JOIN (
                    SELECT locker_number, MAX(id) as max_id 
                    FROM log_loker 
                    GROUP BY locker_number
                ) latest ON l.locker_number = latest.locker_number AND l.id = latest.max_id
            ";
            $resStatus = $conn->query($sqlStatus);
            if ($resStatus) {
                while ($r = $resStatus->fetch_assoc()) {
                    $lockerFisik[$r['locker_number']] = $r;
                }
            }


            // D. Ambil Histori Log
            $logHistory = [];
            $sqlLog = "
                SELECT ll.*, u.Nama AS user_nama, u.PBL AS user_pbl
                FROM log_loker ll
                LEFT JOIN user u ON ll.user_id = u.userid
                ORDER BY ll.id DESC
                LIMIT 100
            ";
            $resLog = $conn->query($sqlLog);
            if ($resLog) {
                while ($r = $resLog->fetch_assoc()) {
                    $logHistory[] = $r;
                }
            }
        }


        // ========================================
        // DATA UNTUK HALAMAN MANAJEMEN USER
        // ========================================
        if ($page == 'users') {
            $users = [];
            $sqlUsers = "SELECT id, userid, Nama, NIM, PBL, email, gender, Angkatan, role 
                         FROM user ORDER BY id ASC";
            $resUsers = $conn->query($sqlUsers);
            if ($resUsers) {
                while ($u = $resUsers->fetch_assoc()) {
                    $users[] = $u;
                }
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
    <title>Simalas - Admin</title>
    <link href="assets/img/brail2.png" rel="icon">
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">


    <style>
        .locker-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; padding: 10px; }
        .locker-box { width: 100%; background-color: #f0f0f0; border: 2px solid #ccc; border-radius: 8px; padding: 15px; position: relative; min-height: 280px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; }
        .locker-number { font-size: 1.5rem; font-weight: 800; color: #5a5c69; text-align: center; margin-bottom: 10px; }
        .status-badge { text-align: center; margin-bottom: 10px; padding: 5px; border-radius: 5px; background: rgba(255,255,255,0.7); font-weight: bold; font-size: 0.8rem; }
        .control-area { margin-top: 5px; border-top: 1px dashed #bbb; padding-top: 10px; }
        .lbl-small { font-size: 0.7rem; font-weight: bold; display: block; margin-bottom: 3px; color: #555; }
        .locker-select { width: 100%; padding: 5px; font-size: 0.8rem; border-radius: 4px; border: 1px solid #ccc; margin-bottom: 10px; }
        .btn-group-custom { display: flex; gap: 5px; }
        .btn-group-custom button { flex: 1; font-size: 0.7rem; padding: 6px 2px; cursor: pointer; }
        @media (max-width: 768px) { .locker-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .locker-grid { grid-template-columns: 1fr; } }
    </style>
</head>


<body id="page-top">
<div id="wrapper">


    <!-- SIDEBAR -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <img src="../assets/img/brail2.png" alt="custom icon" width="40" height="40">
            </div>
            <div class="sidebar-brand-text mx-3">Simalas <sup>2025</sup></div>
        </a>
        <hr class="sidebar-divider my-0">

        <!-- Menu Dashboard User -->
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard User</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Admin Panel</div>


        <!-- Menu Monitor Loker (Dashboard Admin) -->
        <li class="nav-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
            <a class="nav-link" href="admin.php?page=dashboard">
                <i class="fas fa-fw fa-server"></i>
                <span>Monitor Loker</span>
            </a>
        </li>


        <!-- Menu Manajemen User -->
        <li class="nav-item <?php echo ($page == 'users') ? 'active' : ''; ?>">
            <a class="nav-link" href="admin.php?page=users">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Manajemen User</span>
            </a>
        </li>


        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>


    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- TOPBAR -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <form class="form-inline">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                </form>


                <!-- Time Display -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="#" id="timeDisplay"
                           style="border: 2px solid #f4f4f4; border-radius: 12px; padding: 12px 20px; font-size: 18px; color: rgb(15, 15, 15); background-color: #f4f4f4; transition: all 0.3s ease;"
                           onmouseover="this.style.backgroundColor='#e6f0ff'"
                           onmouseout="this.style.backgroundColor='#f0f8ff'">
                            <i class="fas fa-clock me-2"></i><span id="timeText">TIME</span>
                        </a>
                    </li>
                </ul>


                <!-- User Profile -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo htmlspecialchars($username); ?>
                            </span>
                            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>


            <!-- BEGIN PAGE CONTENT -->
            <div class="container-fluid">


                <!-- ============================================ -->
                <!-- TAMPILAN 1: DASHBOARD (Loker) -->
                <!-- ============================================ -->
                <?php if ($page == 'dashboard'): ?>

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Monitor Sistem & Loker</h1>
                        <a href="#" id="generateReport" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>
                    </div>


                    <!-- Grid Status Loker -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Status Loker Real-Time</h6>
                        </div>
                        <div class="card-body">
                            <div class="locker-grid">
                                <?php for ($i = 1; $i <= 4; $i++):
                                    $dataFisik = isset($lockerFisik[$i]) ? $lockerFisik[$i] : null;
                                    $status = $dataFisik ? $dataFisik['status'] : 'UNKNOWN';
                                    $source = $dataFisik ? $dataFisik['source'] : '-';
                                    $hakPBL = isset($lockerHak[$i]) ? $lockerHak[$i] : "";


                                    $bg = "#f8f9fa"; $border = "#ccc"; $textStatus = "Belum Ada Data"; $textColor = "#888";

                                    if ($status == 'DITARUH' || $status == 'IN') {
                                        $bg = "#ffe3e3"; $border = "#e74a3b";
                                        $textStatus = "TERISI (Ada Barang)"; $textColor = "#e74a3b";
                                    } else if ($status == 'DIAMBIL' || $status == 'OUT') {
                                        $bg = "#e3ffe3"; $border = "#1cc88a";
                                        $textStatus = "KOSONG (Available)"; $textColor = "#1cc88a";
                                    }
                                ?>
                                <div class="locker-box" style="background-color: <?php echo $bg; ?>; border-left: 5px solid <?php echo $border; ?>;">
                                    <div class="locker-number">LOKER <?php echo $i; ?></div>

                                    <div class="status-badge" style="color: <?php echo $textColor; ?>;">
                                        <i class="fas fa-circle"></i> <?php echo $textStatus; ?>
                                        <div style="font-size: 0.65rem; color: #555; margin-top:2px;">
                                            Last Source: <?php echo strtoupper($source); ?>
                                        </div>
                                    </div>


                                    <!-- Pengaturan Hak Akses -->
                                    <div class="control-area">
                                        <label class="lbl-small">Hak Akses PBL:</label>
                                        <select class="locker-select locker-select-pbl" data-locker="<?php echo $i; ?>">
                                            <option value="">-- Kosong --</option>
                                            <?php if ($rowsPBL): ?>
                                                <?php foreach ($rowsPBL as $row):
                                                    $val = htmlspecialchars($row['PBL']);
                                                    $sel = ($val == $hakPBL) ? "selected" : "";
                                                ?>
                                                    <option value="<?php echo $val; ?>" <?php echo $sel; ?>>
                                                        <?php echo $val; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>


                                    <!-- Tombol Manual Admin -->
                                    <div class="control-area">
                                        <label class="lbl-small">Manual Override:</label>
                                        <div class="btn-group-custom">
                                            <button type="button" class="btn btn-danger btn-manual"
                                                    data-locker="<?php echo $i; ?>" data-status="IN">
                                                <i class="fas fa-box"></i> ADA
                                            </button>
                                            <button type="button" class="btn btn-success btn-manual"
                                                    data-locker="<?php echo $i; ?>" data-status="OUT">
                                                <i class="fas fa-check"></i> KOSONG
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>


                    <!-- Tabel Log Aktivitas -->
                    <div class="card shadow mb-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Log Aktivitas Terbaru</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Tim / PBL</th>
                                        <th>Nama User</th>
                                        <th>Loker</th>
                                        <th>Status</th>
                                        <th>Waktu Event</th>
                                        <th>Durasi</th>
                                        <th>Source</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($logHistory)): ?>
                                        <?php foreach ($logHistory as $log):
                                            $waktuTampil = ($log['status'] == 'DITARUH' || $log['status'] == 'IN')
                                                ? $log['waktu_masuk'] : $log['waktu_keluar'];
                                            $badgeClass = ($log['status'] == 'DITARUH' || $log['status'] == 'IN')
                                                ? 'badge-danger' : 'badge-success';
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($log['user_pbl'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($log['user_nama'] ?? 'System/Admin'); ?></td>
                                                <td class="text-center font-weight-bold"><?php echo $log['locker_number']; ?></td>
                                                <td class="text-center">
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo $log['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $waktuTampil; ?></td>
                                                <td><?php echo ($log['durasi_detik'] > 0) ? $log['durasi_detik'] . ' det' : '-'; ?></td>
                                                <td><?php echo strtoupper($log['source']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="7" class="text-center">Belum ada data log aktivitas.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                <!-- ============================================ -->
                <!-- TAMPILAN 2: MANAJEMEN USER -->
                <!-- ============================================ -->
                <?php elseif ($page == 'users'): ?>

                    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-users-cog"></i> Manajemen User</h1>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna</h6>
                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalAddUser">
                                <i class="fas fa-user-plus"></i> Tambah User
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="userTable" width="100%">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>UserID</th>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>PBL</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $u): ?>
                                            <tr>
                                                <td><?php echo $u['id']; ?></td>
                                                <td><?php echo htmlspecialchars($u['userid']); ?></td>
                                                <td><?php echo htmlspecialchars($u['Nama']); ?></td>
                                                <td><?php echo htmlspecialchars($u['NIM']); ?></td>
                                                <td><?php echo htmlspecialchars($u['PBL']); ?></td>
                                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($u['role']); ?></span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary btn-edit-user"
                                                            data-id="<?php echo $u['id']; ?>"
                                                            data-userid="<?php echo htmlspecialchars($u['userid']); ?>"
                                                            data-nama="<?php echo htmlspecialchars($u['Nama']); ?>"
                                                            data-nim="<?php echo htmlspecialchars($u['NIM']); ?>"
                                                            data-pbl="<?php echo htmlspecialchars($u['PBL']); ?>"
                                                            data-email="<?php echo htmlspecialchars($u['email']); ?>"
                                                            data-role="<?php echo htmlspecialchars($u['role']); ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <a href="user_manage.php?action=delete&id=<?php echo $u['id']; ?>"
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Hapus user ini?');">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="8" class="text-center">Belum ada data user.</td></tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                <?php endif; ?>


            </div>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Simalas 2025</span>
                </div>
            </div>
        </footer>
    </div>
</div>


<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="logout_button">Logout</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Tambah User -->
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="post" action="user_manage.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah User</h5>
          <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="add">
          <div class="form-group">
            <label>UserID (angka unik)</label>
            <input type="number" name="userid" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Nama</label>
            <input type="text" name="Nama" class="form-control" required>
          </div>
          <div class="form-group">
            <label>NIM</label>
            <input type="text" name="NIM" class="form-control" required>
          </div>
          <div class="form-group">
            <label>PBL</label>
            <input type="text" name="PBL" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Gender</label>
            <input type="text" name="gender" class="form-control">
          </div>
          <div class="form-group">
            <label>Angkatan</label>
            <input type="text" name="Angkatan" class="form-control">
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" class="form-control" required>
              <option value="user">user</option>
              <option value="admin">admin</option>
              <option value="dosen">dosen</option>
            </select>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="Password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="post" action="user_manage.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button class="close" type="button" data-dismiss="modal"><span>×</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="id" id="edit_id">
          <div class="form-group">
            <label>UserID</label>
            <input type="number" name="userid" id="edit_userid" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Nama</label>
            <input type="text" name="Nama" id="edit_nama" class="form-control" required>
          </div>
          <div class="form-group">
            <label>NIM</label>
            <input type="text" name="NIM" id="edit_nim" class="form-control" required>
          </div>
          <div class="form-group">
            <label>PBL</label>
            <input type="text" name="PBL" id="edit_pbl" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" id="edit_email" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Role</label>
            <select name="role" id="edit_role" class="form-control" required>
              <option value="user">user</option>
              <option value="admin">admin</option>
              <option value="dosen">dosen</option>
            </select>
          </div>
          <div class="form-group">
            <label>Password baru (kosongkan jika tidak ganti)</label>
            <input type="password" name="Password" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="../js/demo/generate_pdf.js"></script>
<script src="../js/demo/time.js"></script>
<script src="../js/demo/datatables-demo.js"></script>


<script>
    document.getElementById("logout_button").addEventListener("click", function() {
        window.location.href = "../logout.php";
    });


    $(document).ready(function() {

        // Inisialisasi DataTables
        $('#dataTable').DataTable();
        $('#userTable').DataTable();


        // 1. UPDATE HAK AKSES PBL
        $(document).on('change', '.locker-select-pbl', function() {
            var lockerNum = $(this).data('locker');
            var pblVal = $(this).val();
            var payload = [{ locker: lockerNum, pbl: pblVal }];


            $.ajax({
                url: '../classes/set_loker.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payload),
                success: function(res) {
                    alert("Berhasil mengubah Hak Akses Loker " + lockerNum);
                },
                error: function(xhr, status, error) {
                    alert("Gagal update hak akses: " + error);
                }
            });
        });


        // 2. UPDATE MANUAL BARANG
        $(document).on('click', '.btn-manual', function(e) {
            e.preventDefault();
            var lockerNum = $(this).data('locker');
            var statusVal = $(this).data('status');
            var label = (statusVal === 'IN') ? 'ADA (IN)' : 'KOSONG (OUT)';


            if(!confirm("Anda yakin mengubah status Loker " + lockerNum + " menjadi " + label + " secara manual?")) return;


            $.ajax({
                url: '../classes/admin_set_barang.php',
                type: 'POST',
                data: { locker: lockerNum, status: statusVal },
                success: function(res) {
                    if(res.trim() === 'OK' || res.includes('OK') || res.includes('berhasil')) {
                        alert("Berhasil update status!");
                        location.reload();
                    } else {
                        alert("Server: " + res);
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    alert(" Gagal koneksi ke server: " + error);
                }
            });
        });


        // 3. ISI MODAL EDIT USER
        $(document).on('click', '.btn-edit-user', function() {
            $('#edit_id').val($(this).data('id'));
            $('#edit_userid').val($(this).data('userid'));
            $('#edit_nama').val($(this).data('nama'));
            $('#edit_nim').val($(this).data('nim'));
            $('#edit_pbl').val($(this).data('pbl'));
            $('#edit_email').val($(this).data('email'));
            $('#edit_role').val($(this).data('role'));
            $('#modalEditUser').modal('show');
        });
    });
</script>


</body>
</html>