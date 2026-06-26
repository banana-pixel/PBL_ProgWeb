<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kategori'])) {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    if (!empty($nama_kategori)) {
        $nama_kategori_aman = mysqli_real_escape_string($koneksi, $nama_kategori);
        $kueri_cek = "SELECT id_kategori FROM Kategori WHERE nama_kategori = '$nama_kategori_aman'";
        $hasil_cek = mysqli_query($koneksi, $kueri_cek);

        if ($hasil_cek && mysqli_num_rows($hasil_cek) > 0) {
            $_SESSION['error'] = 'Nama kategori sudah ada!';
            header('Location: admin_kategori.php');
            exit;
        }
        
        $kueri_insert = "INSERT INTO Kategori (nama_kategori) VALUES ('$nama_kategori_aman')";
        if (mysqli_query($koneksi, $kueri_insert)) {
            $_SESSION['success'] = 'Kategori berhasil ditambahkan!';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan kategori.';
        }
        header('Location: admin_kategori.php');
        exit;
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_kategori = (int) $_GET['id'];
    
    $kueri_cek = "SELECT id_pengaduan FROM Pengaduan WHERE id_kategori = $id_kategori";
    $hasil_cek = mysqli_query($koneksi, $kueri_cek);

    if ($hasil_cek && mysqli_num_rows($hasil_cek) > 0) {
        $_SESSION['error'] = 'Gagal menghapus! Kategori ini sedang digunakan oleh laporan pengaduan.';
        header('Location: admin_kategori.php');
        exit;
    }
    
    $kueri_delete = "DELETE FROM Kategori WHERE id_kategori = $id_kategori";
    if (mysqli_query($koneksi, $kueri_delete)) {
        $_SESSION['success'] = 'Kategori berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'Gagal menghapus kategori.';
    }
    header('Location: admin_kategori.php');
    exit;
}

$kueri_list = "SELECT Kategori.*, AVG(Pengaduan.rating) as rata_rating FROM Kategori LEFT JOIN Pengaduan ON Kategori.id_kategori = Pengaduan.id_kategori AND Pengaduan.status = 'Resolve' GROUP BY Kategori.id_kategori";
$result_kategori = mysqli_query($koneksi, $kueri_list);
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

    <title>Master Kategori - E-Complaint</title>

    <meta name="description" content="Master Data Kategori E-Complaint" />

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
              <h4 class="fw-bold py-3 mb-4">Kelola Master Data Kategori</h4>

              <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($_SESSION['success']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
              <?php endif; ?>

              <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <?php echo htmlspecialchars($_SESSION['error']); ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
              <?php endif; ?>
              
              <!-- Form Tambah Kategori -->
              <div class="card mb-4">
                <h5 class="card-header">Tambah Kategori Baru</h5>
                <div class="card-body">
                  <form action="admin_kategori.php" method="POST" class="row g-3 align-items-center">
                    <div class="col-sm-8 col-12">
                      <label class="visually-hidden" for="nama_kategori">Nama Kategori</label>
                      <input
                        type="text"
                        class="form-control"
                        id="nama_kategori"
                        name="nama_kategori"
                        placeholder="Masukkan Nama Kategori Baru (cth: Keuangan, Kebersihan)"
                        required />
                    </div>
                    <div class="col-sm-4 col-12">
                      <button type="submit" name="tambah_kategori" class="btn btn-primary w-100">
                        <i class="bx bx-plus me-1"></i> Tambah Kategori
                      </button>
                    </div>
                  </form>
                </div>
              </div>
              <!--/ Form Tambah Kategori -->

              <!-- Tabel Data Kategori -->
              <div class="card">
                <h5 class="card-header">Daftar Kategori Keluhan</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID Kategori</th>
                        <th>Nama Kategori</th>
                        <th>Rata-rata Rating</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php if (mysqli_num_rows($result_kategori) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_kategori)): ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($row['id_kategori']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                            <td>
                              <?php
                              $rata_rating = $row['rata_rating'];
                              if (is_null($rata_rating)) {
                                  echo "Belum ada penilaian";
                              } else {
                                  echo number_format((float)$rata_rating, 1) . " ⭐";
                              }
                              ?>
                            </td>
                            <td>
                              <a 
                                href="admin_kategori.php?action=delete&id=<?php echo urlencode($row['id_kategori']); ?>" 
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                <i class="bx bx-trash me-1"></i> Hapus
                              </a>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="4" class="text-center text-muted py-4">Belum ada data kategori.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Tabel Data Kategori -->
            </div>
            <!-- / Content -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>
