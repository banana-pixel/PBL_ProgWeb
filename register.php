<!doctype html>

<html lang="en" class="layout-wide customizer-hide" data-assets-path="assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Register Mahasiswa - E-Complaint</title>

  <meta name="description" content="Register Mahasiswa E-Complaint" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="assets/vendor/fonts/iconify-icons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="assets/vendor/css/core.css" />
  <link rel="stylesheet" href="assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- Page CSS -->
  <!-- Page -->
  <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />

  <!-- Helpers -->
  <script src="assets/vendor/js/helpers.js"></script>
  <script src="assets/js/config.js"></script>
  <style>
    :root, [data-bs-theme=light] {
      --bs-primary: #004b87 !important;
      --bs-primary-rgb: 0, 75, 135 !important;
    }
    .bg-primary { background-color: #004b87 !important; }
    .text-primary, .app-brand-text { color: #004b87 !important; }
    .btn-primary { background-color: #004b87 !important; border-color: #004b87 !important; box-shadow: 0 0.125rem 0.25rem 0 rgba(0, 75, 135, 0.4) !important; }
    .btn-primary:hover, .btn-primary:active, .btn-primary:focus { background-color: #003a69 !important; border-color: #003a69 !important; }
    .btn-outline-primary { color: #004b87 !important; border-color: #004b87 !important; }
    .btn-outline-primary:hover { background-color: #004b87 !important; color: #fff !important; }
    .bg-label-primary { background-color: #e6f1fc !important; color: #004b87 !important; }
    .app-brand-logo svg, .app-brand-logo.demo svg { color: #004b87 !important; fill: #004b87 !important; }
  </style>
</head>

<body>
  <!-- Content -->

  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register Card -->
        <div class="card px-sm-6 px-0">
          <div class="card-body">
            <!-- Logo -->
            <div class="text-center mb-3"><img src="assets/img/logo-maranatha.png" alt="Logo UK Maranatha" width="110" style="height: auto; object-fit: contain;">
            </div>
            <div class="app-brand justify-content-center mb-6">
              <a href="login.php" class="app-brand-link gap-2">
                <span class="app-brand-text demo text-heading fw-bold">E-Complaint</span>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-1">Registrasi Mahasiswa</h4>
            <p class="mb-6">Silakan isi data diri Anda untuk mendaftar!</p>

            <form id="formAuthentication" class="mb-6" action="proses_register.php" method="POST">
              <div class="mb-6">
                <label for="nrp" class="form-label">NRP</label>
                <input type="text" class="form-control" id="nrp" name="nrp" placeholder="Masukkan NRP Anda" autofocus
                  required />
              </div>
              <div class="mb-6">
                <label for="nama_mahasiswa" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa"
                  placeholder="Masukkan nama lengkap Anda" required />
              </div>
              <div class="mb-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda"
                  required />
              </div>
              <div class="mb-6">
                <label for="no_hp" class="form-label">No. HP</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp"
                  placeholder="Masukkan nomor HP aktif Anda" required />
              </div>
              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" required />
                  <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                </div>
              </div>
              <button class="btn btn-primary d-grid w-100 mt-6" type="submit">Daftar</button>
            </form>

            <p class="text-center">
              <span>Sudah memiliki akun?</span>
              <a href="login.php">
                <span>Login di sini</span>
              </a>
            </p>
          </div>
        </div>
        <!-- Register Card -->
      </div>
    </div>
  </div>

  <!-- / Content -->

  <!-- Core JS -->
  <script src="assets/vendor/libs/jquery/jquery.js"></script>
  <script src="assets/vendor/libs/popper/popper.js"></script>
  <script src="assets/vendor/js/bootstrap.js"></script>
  <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="assets/vendor/js/menu.js"></script>

  <!-- Main JS -->
  <script src="assets/js/main.js"></script>

  <!-- Place this tag before closing body tag for github widget button. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>