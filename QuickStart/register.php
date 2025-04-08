<?php
        
include('classes/connect.php');
include('classes/signup.php');

$Nama = "";
$NIM = "";
$PBL = "";
$gender = "";
$email = "";

if($_SERVER['REQUEST_METHOD']== 'POST'){

$signup = new Signup();
$result = $signup->evaluate($_POST);

if ($result !="") {

    echo "<div style= 'text-align:center;font-size:12px;color:white;background-color:grey;'>";
    echo"<br>The following errors occured:<br><br>";
    echo $result;
    echo "</div>";

}else{
    header("Location: login.php");
    die;
}

$Nama = $_POST['Nama'];
$NIM = $_POST['NIM'];
$PBL = $_POST['PBL'];
$gender = $_POST['gender'];
$email = $_POST['email'];

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

  <!-- Fonts
  <link href="https://fonts.googleapis.com" rel="preconnect">
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

<body style="background: url('/QuickStart/assets/img/jes.jpg') no-repeat center center; background-size: cover;">

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
                        <img src="/QuickStart/assets/img/sec.png" style="width: 100%; max-width: 500px;">
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
                                        <input type="text" class="form-control form-control-user" id="exampleFirstName"name="Nama"
                                            placeholder="Nama">
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <input type="text" class="form-control form-control-user" id="exampleLastName"name="PBL"
                                            placeholder="PBL">
                                    </div>
                                    <div class="col-sm-12">
                                        <input type="text" class="form-control form-control-user" id="exampleNIM"name="NIM"
                                            placeholder="NIM">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-user" id="exampleInputEmail"name="email"
                                        placeholder="Email Address">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" name="Password" class="form-control form-control-user"
                                            id="exampleInputPassword" placeholder="Password">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                        name="repeat_password" id="exampleRepeatPassword" placeholder="Repeat Password" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="genderSelect"></label>
                                    <select class="form-control" id="genderSelect" name="gender">
                                        <option value="Laki-Laki">Laki-Laki</option>
                                        <option value="Perempuan">Perempuan</option>
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
   

        <!-- Modal -->
        <div class="modal fade" id="passwordMismatchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Kata Sandi Tidak Cocok</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    Kata sandi yang Anda masukkan tidak cocok. Silakan coba lagi.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
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

    
    <script>
        // Menangani pengiriman formulir
        document.getElementById("registrationForm").addEventListener("submit", function(event) {
            const password = document.getElementById("exampleInputPassword").value;
            const confirmPassword = document.getElementById("exampleRepeatPassword").value;


            if (password !== confirmPassword) {
                event.preventDefault(); 
                $('#passwordMismatchModal').modal('show'); 
            }
        });
    </script>

</body>

</html>
