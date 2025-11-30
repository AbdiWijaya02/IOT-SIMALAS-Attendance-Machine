<?php
session_start();
include('classes/connect.php');
include('classes/signup.php');

$Nama = "";
$NIM = "";
$PBL = "";
$gender = "";
$email = "";
$Angkatan = "";
$errorMessage = "";
$showErrorModal = false;
$showSuccessModal = false;

if (isset($_GET['error']) && $_GET['error'] == 'true') {
    $showErrorModal = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signup = new Signup();
    $result = $signup->evaluate($_POST);

    if ($result != "") {
        $showErrorModal = true;
        $errorMessage = $result;
    } else {
        $showSuccessModal = true;
        // Tidak langsung redirect di sini, biarkan JavaScript menangani
    }

    $Nama = $_POST['Nama'];
    $NIM = $_POST['NIM'];
    $PBL = $_POST['PBL'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $Angkatan = $_POST['Angkatan'];
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

    <title>Simalas - Register</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Favicons -->
    <link href="assets/img/brail.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.2/css/bootstrap.min.css'>

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

    <!-- Header -->
    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">
            <a href="index.php" class="logo d-flex align-items-center me-auto">
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
            <a class="btn-getstarted" href="login.php">Login</a>
        </div>
    </header>

    <!-- Animasi Scroll AOS -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    <!-- Container utama -->
    <div class="container" style="margin-top: 130px;">
        <div class="card o-hidden border-0 shadow-lg my-5" data-aos="fade-up">
            <div class="card-body p-0">
                <div class="row">
                    <!-- Kolom untuk gambar -->
                    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
                        <img src="assets/img/sec.png" style="width: 100%; max-width: 500px;">
                    </div>

                    <!-- Kolom untuk form -->
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4" data-aos="fade-up">Create an Account!</h1>
                            </div>
                            <form class="user" method="POST" action="" id="registrationForm" data-aos="fade-up" data-aos-delay="200">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="exampleFirstName" name="Nama"
                                            placeholder="Nama" value="<?php echo htmlspecialchars($Nama); ?>">
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <input type="text" class="form-control form-control-user" id="exampleLastName" name="PBL"
                                            placeholder="PBL" oninput="toUpperCase(this)" value="<?php echo htmlspecialchars($PBL); ?>">
                                    </div>
                                    <script>
                                        function toUpperCase(input) {
                                            input.value = input.value.toUpperCase();
                                        }
                                    </script>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control form-control-user" id="exampleNIM" name="NIM"
                                            placeholder="NIM" value="<?php echo htmlspecialchars($NIM); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail" name="email"
                                        placeholder="Email Address" value="<?php echo htmlspecialchars($email); ?>">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" name="Password" class="form-control form-control-user"
                                            id="exampleInputPassword" placeholder="Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            name="repeat_password" id="exampleRepeatPassword" placeholder="Repeat Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="genderSelect"></label>
                                    <select class="form-control" id="genderSelect" name="gender">
                                        <option value="Laki-Laki" <?php echo ($gender == 'Laki-Laki') ? 'selected' : ''; ?>>Laki-Laki</option>
                                        <option value="Perempuan" <?php echo ($gender == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="angkatanSelect">Angkatan</label>
                                    <select class="form-control" id="angkatanSelect" name="Angkatan">
                                        <option value="2022" <?php echo ($Angkatan == '2022') ? 'selected' : ''; ?>>2022</option>
                                        <option value="2023" <?php echo ($Angkatan == '2023') ? 'selected' : ''; ?>>2023</option>
                                        <option value="2024" <?php echo ($Angkatan == '2024') ? 'selected' : ''; ?>>2024</option>
                                        <option value="2025" <?php echo ($Angkatan == '2025') ? 'selected' : ''; ?>>2025</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user" style="width: 450px; padding: 10px 30px;" data-aos="zoom-in" data-aos-delay="300">
                                    Register Account
                                </button>
                                <hr>
                            </form>
                            <hr>
                            <div class="text-center" data-aos="fade-up" data-aos-delay="600">
                                <a class="small" href="login.php">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Password Tidak Cocok -->
    <div class="modal fade" id="missmatch" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-lg-4">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                        <circle class="path circle" fill="none" stroke="#db3646" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                        <line class="path line" fill="none" stroke="#db3646" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
                        <line class="path line" fill="none" stroke="#db3646" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" X2="34.4" y2="92.2" />
                    </svg>
                    <h4 class="text-danger mt-3">Invalid Register!</h4>
                    <p class="mt-3">Password Tidak Cocok!!!</p>
                    <button type="button" class="btn btn-sm mt-3 btn-danger" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Error Lainnya -->
    <div class="modal fade" id="statusErrorsModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-lg-4">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                        <circle class="path circle" fill="none" stroke="#db3646" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                        <line class="path line" fill="none" stroke="#db3646" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
                        <line class="path line" fill="none" stroke="#db3646" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" X2="34.4" y2="92.2" />
                    </svg>
                    <h4 class="text-danger mt-3">Invalid Register!</h4>
                    <p class="mt-3"><?php echo htmlspecialchars($errorMessage); ?></p>
                    <button type="button" class="btn btn-sm mt-3 btn-danger" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Sukses Register -->
    <div class="modal fade" id="statusSuccessModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body text-center p-lg-4">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                        <circle class="path circle" fill="none" stroke="#198754" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                        <polyline class="path check" fill="none" stroke="#198754" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
                    </svg>
                    <h4 class="text-success mt-3">Oh Yeah!</h4>
                    <p class="mt-3">You have successfully registered!</p>
                    <button type="button" class="btn btn-sm mt-3 btn-success" data-bs-dismiss="modal" id="successModalButton">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages -->
    <script src="js/sb-admin-2.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/js/bootstrap.min.js'></script>

    <script>
        // Menangani pengiriman formulir
        document.getElementById("registrationForm").addEventListener("submit", function(event) {
            const password = document.getElementById("exampleInputPassword").value;
            const confirmPassword = document.getElementById("exampleRepeatPassword").value;

            if (password !== confirmPassword) {
                event.preventDefault();
                $('#missmatch').modal('show');
            }
        });

        $(document).ready(function() {
            <?php if ($showErrorModal): ?>
                $('#statusErrorsModal').modal('show');
            <?php elseif ($showSuccessModal): ?>
                $('#statusSuccessModal').modal('show');

                // Redirect ketika modal sukses ditutup
                $('#statusSuccessModal').on('hidden.bs.modal', function() {
                    window.location.href = "login.php";
                });

                // Atau redirect otomatis setelah 3 detik
                setTimeout(function() {
                    window.location.href = "login.php";
                }, 3000);
            <?php endif; ?>
        });
    </script>

</body>

</html>