<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
include 'helper_visual.php';

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'terbaru';
$filter_status_aman = mysqli_real_escape_string($koneksi, $filter_status);

$query = "SELECT p.*, m.nama_mahasiswa, m.poin 
          FROM Pengaduan p 
          LEFT JOIN Mahasiswa m ON p.nrp = m.nrp";

if ($filter_status !== '') {
    $query .= " WHERE p.status = '$filter_status_aman'";
}

if ($sort_by === 'urgensi') {
    $query .= " ORDER BY FIELD(p.urgensi, 'Tinggi', 'Sedang', 'Rendah') ASC, p.tanggal_lapor DESC";
} elseif ($sort_by === 'terlama') {
    $query .= " ORDER BY p.tanggal_lapor ASC";
} else {
    $query .= " ORDER BY p.tanggal_lapor DESC";
}

$result = mysqli_query($koneksi, $query);
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

    <title>Manajemen Pengaduan - E-Complaint</title>

    <meta name="description" content="Manajemen Pengaduan Mahasiswa E-Complaint" />

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
              <h4 class="fw-bold py-3 mb-0">Manajemen Pengaduan</h4>

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
              <p class="text-muted mb-4">Kelola dan tanggapi seluruh aspirasi serta keluhan mahasiswa Universitas Kristen Maranatha.</p>
              
              <!-- Filter Card -->
              <div class="card mb-4">
                <div class="card-body">
                  <form method="GET" action="manajemen_tiket.php" class="row align-items-end">
                    <div class="col-md-5 mb-3 mb-md-0">
                      <label for="status-filter" class="form-label fw-bold">Filter Status Keluhan</label>
                      <select id="status-filter" name="status" class="form-select">
                        <option value="" <?php echo ($filter_status === '') ? 'selected' : ''; ?>>Semua Status</option>
                        <option value="Pending" <?php echo ($filter_status === 'Pending') ? 'selected' : ''; ?>>Diajukan (Pending)</option>
                        <option value="On Progress" <?php echo ($filter_status === 'On Progress') ? 'selected' : ''; ?>>Diproses (On Progress)</option>
                        <option value="Resolve" <?php echo ($filter_status === 'Resolve') ? 'selected' : ''; ?>>Selesai (Resolve)</option>
                        <option value="Dibatalkan" <?php echo ($filter_status === 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                        <option value="Ditolak" <?php echo ($filter_status === 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                      </select>
                    </div>
                    <div class="col-md-5 mb-3 mb-md-0">
                      <label for="sort-filter" class="form-label fw-bold">Urutan Data</label>
                      <select id="sort-filter" name="sort_by" class="form-select">
                        <option value="terbaru" <?php echo ($sort_by === 'terbaru') ? 'selected' : ''; ?>>Tanggal Lapor: Terbaru</option>
                        <option value="terlama" <?php echo ($sort_by === 'terlama') ? 'selected' : ''; ?>>Tanggal Lapor: Terlama</option>
                        <option value="urgensi" <?php echo ($sort_by === 'urgensi') ? 'selected' : ''; ?>>Urgensi Tertinggi</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter-alt me-1"></i> Filter</button>
                    </div>
                  </form>
                </div>
              </div>
              <!--/ Filter Card -->

              <!-- Tabel Seluruh Pengaduan -->
              <div class="card">
                <h5 class="card-header">Daftar Laporan Pengaduan</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID Tiket</th>
                        <th>Nama Pelapor</th>
                        <th>Tanggal Lapor</th>
                        <th>SLA Countdown</th>
                        <th>Urgensi</th>
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
                            $status_info = get_status_info($status);
                          ?>
                          <tr>
                            <td><strong><?php echo htmlspecialchars($row['id_pengaduan']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['nama_mahasiswa'] ?? 'Mahasiswa (NRP: '.$row['nrp'].')'); ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_lapor'])); ?></td>
                            <td>
                              <?php if (strcasecmp($status, 'resolve') == 0): ?>
                                <span class="badge bg-label-success">Selesai</span>
                              <?php elseif (strcasecmp($status, 'dibatalkan') == 0): ?>
                                <span class="text-muted">-</span>
                              <?php elseif (!empty($row['target_selesai'])): ?>
                                <span class="sla-countdown fw-bold" data-target="<?php echo htmlspecialchars($row['target_selesai']); ?>">--:--:--</span>
                              <?php else: ?>
                                <span class="text-muted">-</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php
                                $urg = $row['urgensi'] ?? 'Sedang';
                                $urg_class = 'bg-label-warning';
                                if ($urg === 'Tinggi') {
                                    $urg_class = 'bg-label-danger';
                                } elseif ($urg === 'Rendah') {
                                    $urg_class = 'bg-label-info';
                                }
                              ?>
                              <span class="badge <?php echo $urg_class; ?>">
                                <?php echo htmlspecialchars($urg); ?>
                              </span>
                            </td>
                            <td>
                              <span class="d-inline-block text-truncate" style="max-width: 300px;" title="<?php echo htmlspecialchars($row['detail_keluhan']); ?>">
                                <?php echo htmlspecialchars($row['detail_keluhan']); ?>
                              </span>
                            </td>
                            <td>
                              <span class="badge <?php echo $status_info['class']; ?>">
                                <?php echo htmlspecialchars($status_info['label']); ?>
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
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $row['id_pengaduan']; ?>">
                                  Detail
                                </button>
                                <?php if (strcasecmp($status, 'pending') == 0): ?>
                                  <a href="proses_admin.php?id=<?php echo urlencode($row['id_pengaduan']); ?>&action=proses" class="btn btn-sm btn-primary">Proses Laporan</a>
                                  <button type="button" class="btn btn-sm btn-danger btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="<?php echo htmlspecialchars($row['id_pengaduan']); ?>"><i class="bx bx-x me-1"></i>Tolak</button>
                                <?php elseif (strcasecmp($status, 'on progress') == 0): ?>
                                  <button type="button" class="btn btn-sm btn-success btn-resolve" data-bs-toggle="modal" data-bs-target="#resolveModal" data-id="<?php echo htmlspecialchars($row['id_pengaduan']); ?>">Selesaikan</button>
                                  <button type="button" class="btn btn-sm btn-danger btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal" data-id="<?php echo htmlspecialchars($row['id_pengaduan']); ?>"><i class="bx bx-x me-1"></i>Tolak</button>
                                <?php elseif (strcasecmp($status, 'resolve') == 0): ?>
                                  <span class="text-muted small">Selesai</span>
                                <?php elseif (strcasecmp($status, 'dibatalkan') == 0): ?>
                                  <span class="badge bg-label-secondary">Dibatalkan</span>
                                <?php elseif (strcasecmp($status, 'ditolak') == 0): ?>
                                  <span class="badge bg-label-danger">Ditolak</span>
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
                                            <td class="fw-bold py-1">Poin Mahasiswa</td>
                                            <td class="py-1">
                                              : <span class="badge bg-warning text-dark fw-bold">
                                                <i class="bx bxs-medal me-1"></i><?php echo number_format($row['poin'] ?? 0); ?> Poin
                                              </span>
                                            </td>
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
                                        <h6 class="fw-semibold mb-1"><i class="bx bx-map me-1 text-primary"></i>Lokasi Spesifik:</h6>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($row['lokasi_spesifik']); ?></p>

                                        <h6 class="fw-semibold mb-1"><i class="bx bx-error-circle me-1 text-primary"></i>Tingkat Urgensi:</h6>
                                        <p class="mb-2">
                                          <?php
                                            $urg = $row['urgensi'] ?? 'Sedang';
                                            $urg_class = 'bg-label-warning';
                                            if ($urg === 'Tinggi') {
                                                $urg_class = 'bg-label-danger';
                                            } elseif ($urg === 'Rendah') {
                                                $urg_class = 'bg-label-info';
                                            }
                                          ?>
                                          <span class="badge <?php echo $urg_class; ?>"><?php echo htmlspecialchars($urg); ?></span>
                                        </p>

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
                                            <span class="fw-bold">Diajukan:</span> <?php echo date('d-m-Y H:i', strtotime($row['tanggal_lapor'])); ?>
                                          </li>
                                          <li class="mb-1">
                                            <i class="bx bx-circle me-1 text-warning"></i>
                                            <span class="fw-bold">Diproses:</span> 
                                            <?php echo !empty($row['waktu_on_progress']) ? date('d-m-Y H:i', strtotime($row['waktu_on_progress'])) : '<span class="text-muted">Belum diproses</span>'; ?>
                                          </li>
                                          <li>
                                            <i class="bx bx-circle me-1 text-success"></i>
                                            <span class="fw-bold">Selesai:</span> 
                                            <?php echo !empty($row['waktu_resolve']) ? date('d-m-Y H:i', strtotime($row['waktu_resolve'])) : '<span class="text-muted">Belum selesai</span>'; ?>
                                          </li>
                                        </ul>
                                      </div>

                                      <?php if (!empty($row['tanggapan_admin'])): ?>
                                        <hr class="my-2" />
                                        <div class="mb-3">
                                          <h6 class="fw-semibold mb-1">
                                            <i class="bx bx-comment-detail me-1 text-primary"></i>
                                            <?php echo (strcasecmp($status, 'ditolak') == 0) ? 'Alasan Penolakan:' : 'Tanggapan & Penyelesaian Admin:'; ?>
                                          </h6>
                                          <div class="p-2 bg-light rounded text-muted small">
                                            <?php echo nl2br(htmlspecialchars($row['tanggapan_admin'])); ?>
                                          </div>
                                          <?php if (!empty($row['foto_bukti_selesai'])): ?>
                                            <div class="mt-2">
                                              <span class="fw-semibold d-block mb-1 small text-muted">Foto Bukti Penanganan:</span>
                                              <a href="uploads/<?php echo htmlspecialchars($row['foto_bukti_selesai']); ?>" target="_blank">
                                                <img src="uploads/<?php echo htmlspecialchars($row['foto_bukti_selesai']); ?>" class="img-fluid rounded mt-1" style="max-height: 200px;" alt="Foto Bukti Penanganan" />
                                              </a>
                                            </div>
                                          <?php endif; ?>
                                        </div>
                                      <?php endif; ?>

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
                  <form action="proses_admin.php?action=resolve" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                      <!-- Hidden ID Pengaduan -->
                      <input type="hidden" name="id_pengaduan" id="modal_id_pengaduan" value="" />
                      
                      <div class="row">
                        <div class="col mb-3">
                          <label for="tanggapan_admin" class="form-label fw-bold">Tanggapan Admin <span class="text-danger">*</span></label>
                          <textarea class="form-control" id="tanggapan_admin" name="tanggapan_admin" rows="4" placeholder="Berikan tanggapan penyelesaian untuk laporan ini..." required></textarea>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col">
                          <label for="foto_bukti_selesai" class="form-label fw-bold">Foto Bukti Penanganan <span class="text-muted small">(Opsional)</span></label>
                          <input class="form-control" type="file" id="foto_bukti_selesai" name="foto_bukti_selesai" accept="image/*" />
                          <small class="text-muted d-block mt-1">Format: JPG, PNG. Maksimal 5 MB.</small>
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

            <!-- Modal Tolak Laporan -->
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                      <i class="bx bx-x-circle me-2 text-danger"></i>Tolak Laporan Pengaduan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form action="proses_admin.php?action=reject" method="POST">
                    <div class="modal-body">
                      <input type="hidden" name="id_pengaduan" id="reject_id_pengaduan" value="" />

                      <div class="alert alert-warning d-flex align-items-start gap-2 py-2" role="alert">
                        <i class="bx bx-info-circle flex-shrink-0 mt-1"></i>
                        <span class="small">Alasan penolakan akan <strong>terlihat oleh mahasiswa</strong> di halaman Riwayat Pengaduan mereka. Pastikan alasan yang diberikan jelas dan edukatif.</span>
                      </div>

                      <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2 mb-2">
                          <div class="form-check">
                            <input class="form-check-input reject-preset" type="radio" name="preset_alasan" id="preset1" value="Di luar wewenang kampus. Mohon menghubungi pihak yang berwenang.">
                            <label class="form-check-label small" for="preset1">Di luar wewenang kampus</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input reject-preset" type="radio" name="preset_alasan" id="preset2" value="Tidak ada bukti pendukung yang jelas. Mohon ajukan ulang disertai foto atau dokumentasi.">
                            <label class="form-check-label small" for="preset2">Tidak ada bukti yang jelas</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input reject-preset" type="radio" name="preset_alasan" id="preset3" value="Laporan mengandung bahasa yang tidak pantas. Mohon ajukan ulang dengan bahasa yang sopan.">
                            <label class="form-check-label small" for="preset3">Bahasa tidak pantas</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input reject-preset" type="radio" name="preset_alasan" id="preset4" value="Laporan duplikat. Pengaduan serupa sudah pernah diajukan sebelumnya.">
                            <label class="form-check-label small" for="preset4">Laporan duplikat</label>
                          </div>
                          <div class="form-check">
                            <input class="form-check-input reject-preset" type="radio" name="preset_alasan" id="preset_custom" value="custom">
                            <label class="form-check-label small" for="preset_custom">Alasan lain (isi manual)</label>
                          </div>
                        </div>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="3" placeholder="Tulis alasan penolakan di sini..." required></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                      <button type="submit" class="btn btn-danger"><i class="bx bx-x-circle me-1"></i>Tolak Laporan</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!--/ Modal Tolak Laporan -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>

            <script>
              document.addEventListener('DOMContentLoaded', function() {
                // Resolve button dynamic modal fields
                var resolveButtons = document.querySelectorAll('.btn-resolve');
                resolveButtons.forEach(function(button) {
                  button.addEventListener('click', function() {
                    var id_pengaduan = this.getAttribute('data-id');
                    document.getElementById('modal_id_pengaduan').value = id_pengaduan;
                  });
                });

                // Reject button: populate hidden ID + reset form
                var rejectButtons = document.querySelectorAll('.btn-reject');
                rejectButtons.forEach(function(button) {
                  button.addEventListener('click', function() {
                    var id = this.getAttribute('data-id');
                    document.getElementById('reject_id_pengaduan').value = id;
                    // Reset form on open
                    document.getElementById('alasan_penolakan').value = '';
                    document.querySelectorAll('.reject-preset').forEach(function(r){ r.checked = false; });
                  });
                });

                // Preset radio → auto-fill textarea
                document.querySelectorAll('.reject-preset').forEach(function(radio) {
                  radio.addEventListener('change', function() {
                    var textarea = document.getElementById('alasan_penolakan');
                    if (this.value === 'custom') {
                      textarea.value = '';
                      textarea.focus();
                    } else {
                      textarea.value = this.value;
                    }
                  });
                });

                // SLA Countdown Timer Logic
                function initSLACountdowns() {
                  const elements = document.querySelectorAll('.sla-countdown');
                  if (elements.length === 0) return;

                  function updateTimers() {
                    const now = new Date().getTime();

                    elements.forEach(el => {
                      const targetStr = el.getAttribute('data-target');
                      if (!targetStr) return;

                      // Parse target string (Format: YYYY-MM-DD HH:MM:SS)
                      // Replace space with T to make it fully ISO-compliant for browsers
                      const targetTime = new Date(targetStr.replace(' ', 'T')).getTime();
                      const distance = targetTime - now;

                      if (isNaN(distance)) {
                        el.textContent = 'Invalid Target';
                        return;
                      }

                      if (distance < 0) {
                        // SLA Breached! Make it red
                        el.style.setProperty('color', '#ff3e1d', 'important'); // Sneat danger/red color
                        
                        const overdue = Math.abs(distance);
                        const hours = Math.floor(overdue / (1000 * 60 * 60));
                        const minutes = Math.floor((overdue % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((overdue % (1000 * 60)) / 1000);

                        el.textContent = `Breached (-${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')})`;
                      } else {
                        // In time countdown
                        const hours = Math.floor(distance / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        el.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                        el.style.color = ''; // Default style
                      }
                    });
                  }

                  updateTimers();
                  setInterval(updateTimers, 1000);
                }

                initSLACountdowns();
              });
            </script>
