<?php
session_start();

include("classes/connect.php");
include("classes/login.php");

$NIM = "";
$Password = "";
$showErrorModal = false;
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = new Login();
    $result = $login->evaluate($_POST);

    $NIM = $_POST['NIM'];
    $Password = $_POST['Password'];

    if ($result != "") {
        $showErrorModal = true;
        $errorMessage = $result;
    } else {
        // Redirect berdasarkan role
        if (isset($_SESSION['aiot_role'])) {
            if ($_SESSION['aiot_role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($_SESSION['aiot_role'] == 'dosen') {
                header("Location: dosen/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            die;
        }
    }
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

    <title>Simalas_AIoT - Login</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Favicons -->
    <link href="assets/img/brail.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> -->

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">


</head>

<body style="background: url('assets/img/jes.jpg') no-repeat center center; background-size: cover;">


    <body class="bg-gradient-primary">
        <!-- Header -->
        <header id="header" class="header d-flex align-items-center fixed-top">
            <div class="container-fluid container-xl position-relative d-flex align-items-center">
                <a href="index.php" class="logo d-flex align-items-center me-auto">
                    <!-- <img src="assets/img/R.png" alt=""> -->
                    <img src="assets/img/log.png" alt="">
                    <h1 class="sitename">Simalas</h1>
                </a>
                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="index.php#hero">Home</a></li>
                        <li><a href="index.php#about">About</a></li>
                        <li><a href="index.php#features">Cara Penggunaan</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
                </nav>
            </div>
        </header>

        <!-- Animasi Scroll AOS -->
        <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
        <script>
            AOS.init();
        </script>

        <!-- Container utama -->
        <div class="container" style="margin-top: 200px;">
            <div class="card o-hidden border-0 shadow-lg my-5" data-aos="fade-up">
                <div class="card-body p-0">
                    <div class="row">
                        <!-- Kolom untuk gambar -->
                        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center"> <img src="assets/img/por.png" style="width: 100%; max-width: 500px;" /> </div> <!-- Kolom untuk form -->
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                </div>
                                <form class="user" method="POST" action="" data-aos="fade-up" data-aos-delay="200">
                                    <div class="form-group">
                                        <input type="text" name="NIM" class="form-control form-control-user" placeholder="Enter NIM">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="Password" class="form-control form-control-user" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox small">
                                            <input type="checkbox" class="custom-control-input" id="customCheck">
                                            <label class="custom-control-label" for="customCheck">Remember Me</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Login
                                    </button>
                                    <hr>
                                </form>

                                <hr>
                                <div class="text-center">
                                    <a class="small" href="register.php">Create an Account!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        </div>

        </div>

        <!-- Modal -->
        <div class="modal fade" id="passwordMismatchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Login Gagal</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $errorMessage; ?></p> <!-- Tampilkan pesan error -->
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>

        <script>
            $(document).ready(function() {
                <?php if ($showErrorModal): ?>
                    $('#passwordMismatchModal').modal('show');
                <?php endif; ?>
            });
        </script>

    </body>

</html>