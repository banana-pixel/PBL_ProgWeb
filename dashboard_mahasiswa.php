<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$nrp = $_SESSION['nrp'];

$query_pending = "SELECT COUNT(*) AS total FROM Pengaduan WHERE nrp = '$nrp' AND status = 'Pending'";
$result_pending = mysqli_query($koneksi, $query_pending);
$row_pending = mysqli_fetch_assoc($result_pending);
$tiket_pending = $row_pending['total'];

$query_proses = "SELECT COUNT(*) AS total FROM Pengaduan WHERE nrp = '$nrp' AND status = 'On Progress'";
$result_proses = mysqli_query($koneksi, $query_proses);
$row_proses = mysqli_fetch_assoc($result_proses);
$tiket_proses = $row_proses['total'];

$query_resolve = "SELECT COUNT(*) AS total FROM Pengaduan WHERE nrp = '$nrp' AND status = 'Resolve'";
$result_resolve = mysqli_query($koneksi, $query_resolve);
$row_resolve = mysqli_fetch_assoc($result_resolve);
$tiket_resolve = $row_resolve['total'];

$query_terbaru = "SELECT * FROM Pengaduan WHERE nrp = '$nrp' ORDER BY tanggal_lapor DESC LIMIT 5";
$result_terbaru = mysqli_query($koneksi, $query_terbaru);
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

    <title>Dashboard Mahasiswa - E-Complaint</title>

    <meta name="description" content="Dashboard Pengaduan Mahasiswa E-Complaint" />

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
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

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
              <!-- Welcome Banner Card -->
              <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                  <h4 class="card-title text-white mb-1">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_mahasiswa'] ?? 'Mahasiswa'); ?>!</h4>
                  <p class="card-text">Ada kendala fasilitas atau layanan? Laporkan di sini!</p>
                  <a href="buat_tiket.php" class="btn btn-white bg-white text-primary fw-bold mt-2"><i class="bx bx-plus me-1"></i> Buat Pengaduan</a>
                </div>
              </div>
              
              <!-- Statistik Cards -->
              <div class="row mb-6">
                <!-- Tiket Pending -->
                <div class="col-lg-4 col-md-12 col-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-danger text-danger"><i class="bx bx-time bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Tiket Pending</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_pending; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Tiket On Progress -->
                <div class="col-lg-4 col-md-12 col-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-warning text-warning"><i class="bx bx-loader-circle bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Tiket On Progress</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_proses; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Tiket Resolved -->
                <div class="col-lg-4 col-md-12 col-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-success text-success"><i class="bx bx-check-circle bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Tiket Resolved</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_resolve; ?></h4>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Statistik Cards -->

              <!-- Tabel Pengaduan Terbaru Card -->
              <div class="card mb-4">
                <h5 class="card-header fw-bold">Pengaduan Terbaru</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID Tiket</th>
                        <th>Tanggal</th>
                        <th>Detail Keluhan</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php if (mysqli_num_rows($result_terbaru) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result_terbaru)): ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($row['id_pengaduan']); ?></strong></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_lapor'])); ?></td>
                            <td>
                              <span class="d-inline-block text-truncate" style="max-width: 300px;">
                                <?php echo htmlspecialchars($row['detail_keluhan']); ?>
                              </span>
                            </td>
                            <td>
                              <?php
                              $status = $row['status'];
                              if ($status == 'Pending') {
                                  echo '<span class="badge bg-label-danger">Pending</span>';
                              } elseif ($status == 'On Progress') {
                                  echo '<span class="badge bg-label-warning">On Progress</span>';
                              } elseif ($status == 'Resolve') {
                                  echo '<span class="badge bg-label-success">Resolve</span>';
                              } else {
                                  echo '<span class="badge bg-label-secondary">' . htmlspecialchars($status) . '</span>';
                              }
                              ?>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="4" class="text-center text-muted py-4">Belum ada pengaduan terbaru.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Tabel Pengaduan Terbaru Card -->
            </div>
            <!-- / Content -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>
