<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$nrp = $_SESSION['nrp'];

$query_kategori = mysqli_query($koneksi, "SELECT * FROM Kategori ORDER BY nama_kategori ASC");
?>
<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Buat Tiket Pengaduan - E-Complaint</title>

    <meta name="description" content="Kirim pengaduan baru" />

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
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        
        <!-- Sidebar (Menu) -->
        <?php include 'sidebar.php'; ?>
        <!-- / Sidebar (Menu) -->

        <!-- Layout container -->
        <div class="layout-page">
          
          <!-- Navbar -->
          <?php include 'navbar.php'; ?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <h4 class="fw-bold py-3 mb-0">Buat Tiket Pengaduan Baru</h4>
              <p class="text-muted mb-4">Laporkan kendala fasilitas atau pelayanan yang Anda alami.</p>
              
              <!-- Form Pengaduan Card -->
              <div class="card mb-4">
                <h5 class="card-header">Form Pengaduan Baru</h5>
                <div class="card-body">
                  <form action="proses_pengaduan.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                      <label for="kategori" class="form-label">Kategori</label>
                      <select class="form-select" id="kategori" name="kategori" required>
                        <option value="" selected disabled>Pilih Kategori Keluhan</option>
                        <?php if (mysqli_num_rows($query_kategori) > 0): ?>
                          <?php while ($row_kat = mysqli_fetch_assoc($query_kategori)): ?>
                            <option value="<?php echo htmlspecialchars($row_kat['id_kategori']); ?>">
                              <?php echo htmlspecialchars($row_kat['nama_kategori']); ?>
                            </option>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <option value="1">Fasilitas</option>
                          <option value="2">Akademik</option>
                          <option value="3">Keamanan</option>
                        <?php endif; ?>
                      </select>
                    </div>
                    
                    <div class="mb-3">
                      <label for="detail_keluhan" class="form-label">Detail Keluhan</label>
                      <textarea class="form-control" id="detail_keluhan" name="detail_keluhan" rows="4" placeholder="Tuliskan secara lengkap detail keluhan Anda di sini..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                      <label for="foto" class="form-label">Foto Pendukung</label>
                      <input class="form-control" type="file" id="foto" name="foto_pendukung" accept="image/*" />
                      <div class="form-text text-muted">Format file yang diperbolehkan: JPG, JPEG, PNG.</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Kirim Pengaduan</button>
                  </form>
                </div>
              </div>
              <!--/ Form Pengaduan Card -->
            </div>
            <!-- / Content -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>
