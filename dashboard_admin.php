<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$query = "SELECT p.*, m.nama_mahasiswa 
          FROM Pengaduan p 
          LEFT JOIN Mahasiswa m ON p.nrp = m.nrp 
          ORDER BY p.tanggal_lapor DESC";
$result = mysqli_query($koneksi, $query);

$query_total = "SELECT COUNT(*) AS total FROM Pengaduan";
$result_total = mysqli_query($koneksi, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_tiket = $row_total['total'];

$query_pending = "SELECT COUNT(*) AS total FROM Pengaduan WHERE status = 'Pending'";
$result_pending = mysqli_query($koneksi, $query_pending);
$row_pending = mysqli_fetch_assoc($result_pending);
$tiket_pending = $row_pending['total'];

$query_progress = "SELECT COUNT(*) AS total FROM Pengaduan WHERE status = 'On Progress'";
$result_progress = mysqli_query($koneksi, $query_progress);
$row_progress = mysqli_fetch_assoc($result_progress);
$tiket_progress = $row_progress['total'];

$query_resolve = "SELECT COUNT(*) AS total FROM Pengaduan WHERE status = 'Resolve'";
$result_resolve = mysqli_query($koneksi, $query_resolve);
$row_resolve = mysqli_fetch_assoc($result_resolve);
$tiket_resolve = $row_resolve['total'];
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

    <title>Dashboard Admin - E-Complaint</title>

    <meta name="description" content="Dashboard Admin E-Complaint" />

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
                  <h4 class="card-title text-white mb-1">Dashboard Admin: Admin Dekanat</h4>
                  <p class="card-text">Kelola, pantau, dan evaluasi seluruh pengaduan mahasiswa Universitas Kristen Maranatha secara real-time.</p>
                </div>
              </div>
              
              <!-- Statistik Cards -->
              <div class="row mb-6">
                <!-- Total Pengaduan -->
                <div class="col-lg-3 col-md-6 col-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-primary text-primary"><i class="bx bx-envelope bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Total Pengaduan</p>
                      <h4 class="card-title mb-0"><?php echo $total_tiket; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Pending -->
                <div class="col-lg-3 col-md-6 col-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-danger text-danger"><i class="bx bx-time bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Pending</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_pending; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- On Progress -->
                <div class="col-lg-3 col-md-6 col-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-warning text-warning"><i class="bx bx-loader-circle bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">On Progress</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_progress; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Resolved -->
                <div class="col-lg-3 col-md-6 col-6 mb-6">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-success text-success"><i class="bx bx-check-circle bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Resolved</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_resolve; ?></h4>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Statistik Cards -->

              <!-- Tabel Seluruh Pengaduan -->
              <div class="card">
                <h5 class="card-header">Daftar Seluruh Laporan Pengaduan</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID Tiket</th>
                        <th>Nama Pelapor</th>
                        <th>Tanggal Lapor</th>
                        <th>Detail Keluhan</th>
                        <th>Status</th>
                        <th>Rating</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                          <?php
                            $status = $row['status'];
                            $badge_class = 'bg-label-secondary';
                            if (strcasecmp($status, 'pending') == 0) {
                                $badge_class = 'bg-label-danger';
                            } elseif (strcasecmp($status, 'on progress') == 0) {
                                $badge_class = 'bg-label-warning';
                            } elseif (strcasecmp($status, 'resolve') == 0) {
                                $badge_class = 'bg-label-success';
                            }
                          ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($row['id_pengaduan']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['nama_mahasiswa'] ?? 'Mahasiswa (NRP: '.$row['nrp'].')'); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_lapor'])); ?></td>
                            <td>
                              <span class="d-inline-block text-truncate" style="max-width: 300px;" title="<?php echo htmlspecialchars($row['detail_keluhan']); ?>">
                                <?php echo htmlspecialchars($row['detail_keluhan']); ?>
                              </span>
                            </td>
                            <td>
                              <span class="badge <?php echo $badge_class; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                              </span>
                            </td>
                            <td>
                              <?php if (!is_null($row['rating'])): ?>
                                <span class="badge bg-label-primary"><i class="bx bxs-star me-1 text-warning"></i><?php echo htmlspecialchars($row['rating']); ?>/5</span>
                              <?php else: ?>
                                <span class="text-muted">-</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <div class="d-flex gap-1 align-items-center">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $row['id_pengaduan']; ?>">
                                  Detail
                                </button>
                                <?php if (strcasecmp($status, 'pending') == 0): ?>
                                  <a href="proses_admin.php?id=<?php echo urlencode($row['id_pengaduan']); ?>&action=proses" class="btn btn-sm btn-info">Proses Laporan</a>
                                <?php elseif (strcasecmp($status, 'on progress') == 0): ?>
                                  <button type="button" class="btn btn-sm btn-success btn-resolve" data-bs-toggle="modal" data-bs-target="#resolveModal" data-id="<?php echo htmlspecialchars($row['id_pengaduan']); ?>">Selesaikan</button>
                                <?php elseif (strcasecmp($status, 'resolve') == 0): ?>
                                  <span class="text-muted small">Selesai</span>
                                <?php endif; ?>
                              </div>

                              <!-- Modal Detail Admin -->
                              <div class="modal fade" id="detailModal<?php echo $row['id_pengaduan']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title fw-bold" id="detailModalTitle<?php echo $row['id_pengaduan']; ?>">
                                        Detail Pengaduan - <?php echo htmlspecialchars($row['id_pengaduan']); ?>
                                      </h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="white-space: normal;">
                                      
                                      <!-- Informasi Pelapor -->
                                      <div class="mb-3">
                                        <h6 class="fw-semibold mb-1"><i class="bx bx-user me-1 text-primary"></i>Informasi Pelapor:</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                          <tr>
                                            <td style="width: 120px;" class="fw-bold py-1">Nama Mahasiswa</td>
                                            <td class="py-1">: <?php echo htmlspecialchars($row['nama_mahasiswa'] ?? 'Mahasiswa'); ?></td>
                                          </tr>
                                          <tr>
                                            <td class="fw-bold py-1">NRP</td>
                                            <td class="py-1">: <?php echo htmlspecialchars($row['nrp']); ?></td>
                                          </tr>
                                          <tr>
                                            <td class="fw-bold py-1">Tanggal Lapor</td>
                                            <td class="py-1">: <?php echo date('d-m-Y H:i', strtotime($row['tanggal_lapor'])); ?></td>
                                          </tr>
                                        </table>
                                      </div>

                                      <hr class="my-2" />

                                      <!-- Detail Keluhan -->
                                      <div class="mb-3">
                                        <h6 class="fw-semibold mb-1"><i class="bx bx-detail me-1 text-primary"></i>Detail Keluhan:</h6>
                                        <p class="text-muted mb-2" style="white-space: pre-wrap;"><?php echo htmlspecialchars($row['detail_keluhan']); ?></p>
                                        <?php if (!empty($row['foto_pendukung'])): ?>
                                          <div class="mb-2">
                                            <span class="fw-semibold d-block mb-1">Foto Bukti Fisik:</span>
                                            <a href="uploads/<?php echo htmlspecialchars($row['foto_pendukung']); ?>" target="_blank">
                                              <img src="uploads/<?php echo htmlspecialchars($row['foto_pendukung']); ?>" class="img-fluid rounded mt-2" style="max-height: 250px;" alt="Foto Bukti Fisik" />
                                            </a>
                                          </div>
                                        <?php endif; ?>
                                      </div>

                                      <hr class="my-2" />

                                      <!-- Log Timestamp -->
                                      <div class="mb-3">
                                        <h6 class="fw-semibold mb-2"><i class="bx bx-time me-1 text-primary"></i>Catatan Waktu Pelacakan (Log Timestamp):</h6>
                                        <ul class="list-unstyled mb-0 ps-2">
                                          <li class="mb-1">
                                            <i class="bx bx-circle me-1 text-primary"></i>
                                            <span class="fw-bold">Laporan Masuk:</span> <?php echo date('d-m-Y H:i', strtotime($row['tanggal_lapor'])); ?>
                                          </li>
                                          <li class="mb-1">
                                            <i class="bx bx-circle me-1 text-warning"></i>
                                            <span class="fw-bold">Mulai Diproses:</span> 
                                            <?php echo !empty($row['waktu_on_progress']) ? date('d-m-Y H:i', strtotime($row['waktu_on_progress'])) : '<span class="text-muted">Belum diproses</span>'; ?>
                                          </li>
                                          <li>
                                            <i class="bx bx-circle me-1 text-success"></i>
                                            <span class="fw-bold">Selesai (Resolve):</span> 
                                            <?php echo !empty($row['waktu_resolve']) ? date('d-m-Y H:i', strtotime($row['waktu_resolve'])) : '<span class="text-muted">Belum selesai</span>'; ?>
                                          </li>
                                        </ul>
                                      </div>

                                      <!-- Log Penilaian -->
                                      <?php if (strcasecmp($status, 'resolve') == 0): ?>
                                        <hr class="my-2" />
                                        <div class="mb-1">
                                          <h6 class="fw-semibold mb-2"><i class="bx bx-star me-1 text-primary"></i>Evaluasi Layanan (Log Penilaian):</h6>
                                          <?php if (!is_null($row['rating'])): ?>
                                            <div class="mb-1">
                                              <span class="badge bg-label-primary p-2 d-inline-flex align-items-center">
                                                <i class="bx bxs-star me-1 text-warning"></i>
                                                Rating: <?php echo htmlspecialchars($row['rating']); ?> / 5 Bintang
                                              </span>
                                            </div>
                                            <?php if (!empty($row['komentar_mahasiswa'])): ?>
                                              <div class="p-2 bg-light rounded text-muted fst-italic small mt-1">
                                                "<?php echo htmlspecialchars($row['komentar_mahasiswa']); ?>"
                                              </div>
                                            <?php endif; ?>
                                          <?php else: ?>
                                            <span class="text-muted small">Mahasiswa belum memberikan penilaian & ulasan.</span>
                                          <?php endif; ?>
                                        </div>
                                      <?php endif; ?>

                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                        <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="7" class="text-center text-muted py-4">Belum ada data pengaduan masuk.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Tabel Seluruh Pengaduan -->
            </div>
            <!-- / Content -->

            <!-- Modal Selesaikan Laporan -->
            <div class="modal fade" id="resolveModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Selesaikan Pengaduan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="proses_admin.php?action=resolve" method="POST">
                    <div class="modal-body">
                      <!-- Hidden ID Pengaduan -->
                      <input type="hidden" name="id_pengaduan" id="modal_id_pengaduan" value="" />
                      
                      <div class="row">
                        <div class="col mb-3">
                          <label for="tanggapan_admin" class="form-label">Tanggapan Admin</label>
                          <textarea class="form-control" id="tanggapan_admin" name="tanggapan_admin" rows="4" placeholder="Berikan tanggapan penyelesaian untuk laporan ini..." required></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-success">Selesaikan Laporan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!--/ Modal Selesaikan Laporan -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>

            <script>
              document.addEventListener('DOMContentLoaded', function() {
                var resolveButtons = document.querySelectorAll('.btn-resolve');
                resolveButtons.forEach(function(button) {
                  button.addEventListener('click', function() {
                    var id_pengaduan = this.getAttribute('data-id');
                    document.getElementById('modal_id_pengaduan').value = id_pengaduan;
                  });
                });
              });
            </script>
