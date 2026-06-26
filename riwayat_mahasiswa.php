<?php
session_start();
include 'koneksi.php';
include 'helper_visual.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}
$nrp = $_SESSION['nrp'];
$nrp_aman = mysqli_real_escape_string($koneksi, $nrp);

$kueri = "SELECT p.*, k.nama_kategori 
          FROM Pengaduan p 
          LEFT JOIN Kategori k ON p.id_kategori = k.id_kategori 
          WHERE p.nrp = '$nrp_aman' 
          ORDER BY p.tanggal_lapor DESC";
$result = mysqli_query($koneksi, $kueri);
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

    <title>Riwayat Pengaduan Mahasiswa - E-Complaint</title>

    <meta name="description" content="Riwayat Pengaduan Mahasiswa E-Complaint" />

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
              <h4 class="fw-bold py-3 mb-4">Riwayat Pengaduan Anda</h4>
              
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

              <!-- Tabel Riwayat Pengaduan -->
              <div class="card">
                <h5 class="card-header">Daftar Tiket Pengaduan</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID Tiket</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Status</th>
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
                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_lapor'])); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_kategori'] ?? 'Lain-lain'); ?></td>
                            <td>
                              <span class="badge <?php echo $status_info['class']; ?>">
                                <?php echo htmlspecialchars($status_info['label']); ?>
                              </span>
                            </td>
                            <td>
                              <?php if (strcasecmp($status, 'resolve') == 0): ?>
                                <?php if (is_null($row['rating'])): ?>
                                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['id_pengaduan']; ?>">
                                    <i class="bx bx-star me-1"></i> Beri Ulasan
                                  </button>
                                <?php else: ?>
                                  <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['id_pengaduan']; ?>">
                                    <i class="bx bx-comment-detail me-1"></i> Detail & Ulasan
                                  </button>
                                <?php endif; ?>
                              <?php elseif (strcasecmp($status, 'ditolak') == 0): ?>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['id_pengaduan']; ?>">
                                  <i class="bx bx-info-circle me-1"></i> Lihat Alasan
                                </button>
                              <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['id_pengaduan']; ?>">
                                  <i class="bx bx-time-five me-1"></i> Lacak Tiket
                                </button>
                              <?php endif; ?>
                              <?php if (strcasecmp($status, 'pending') == 0): ?>
                                <a href="proses_batal.php?id=<?php echo urlencode($row['id_pengaduan']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin membatalkan laporan ini?')">
                                  Batalkan
                                </a>
                              <?php endif; ?>

                              <!-- Modal Detail & Timeline -->
                              <div class="modal fade" id="modalDetail<?php echo $row['id_pengaduan']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title fw-bold" id="modalDetailTitle<?php echo $row['id_pengaduan']; ?>">
                                        Lacak Tiket - <?php echo htmlspecialchars($row['id_pengaduan']); ?>
                                      </h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body" style="white-space: normal;">
                                      
                                      <!-- Detail Info Singkat -->
                                      <div class="mb-4">
                                        <h6 class="fw-semibold mb-1">Lokasi Spesifik:</h6>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($row['lokasi_spesifik']); ?></p>
                                        
                                        <h6 class="fw-semibold mb-1">Tingkat Urgensi:</h6>
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
                                        
                                        <h6 class="fw-semibold mb-1">Detail Keluhan:</h6>
                                        <p class="text-muted mb-2"><?php echo nl2br(htmlspecialchars($row['detail_keluhan'])); ?></p>
                                        <?php if (!empty($row['foto_pendukung'])): ?>
                                          <div class="mb-2">
                                            <span class="fw-semibold d-block mb-1">Foto Pendukung:</span>
                                            <a href="uploads/<?php echo htmlspecialchars($row['foto_pendukung']); ?>" target="_blank">
                                              <img src="uploads/<?php echo htmlspecialchars($row['foto_pendukung']); ?>" class="img-thumbnail" style="max-height: 150px;" alt="Foto Pendukung" />
                                            </a>
                                          </div>
                                        <?php endif; ?>
                                      </div>

                                      <hr class="my-3" />

                                      <!-- Vertical Timeline UI -->
                                      <h6 class="fw-bold mb-3"><i class="bx bx-git-commit me-1"></i> Timeline Pelacakan</h6>
                                      
                                      <div class="position-relative ps-4 mb-4" style="border-left: 2px dashed #eceef1; margin-left: 10px;">
                                        
                                        <!-- Node 1: Laporan Masuk -->
                                        <div class="mb-4 position-relative">
                                          <!-- Bullet point -->
                                          <div class="position-absolute" style="left: -25px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background-color: #696cff;"></div>
                                          <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-primary">Laporan Diajukan</span>
                                            <small class="text-muted"><?php echo date('d M Y H:i', strtotime($row['tanggal_lapor'])); ?></small>
                                          </div>
                                          <p class="text-muted small mb-0">Pengaduan Anda telah terkirim dan masuk antrean sistem.</p>
                                        </div>
                                        
                                        <!-- Node 2: Sedang Diproses -->
                                        <div class="mb-4 position-relative">
                                          <?php 
                                          $is_on_progress = !is_null($row['waktu_on_progress']); 
                                          $color_progress = $is_on_progress ? '#ffab00' : '#d9dee3';
                                          ?>
                                          <!-- Bullet point -->
                                          <div class="position-absolute" style="left: -25px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background-color: <?php echo $color_progress; ?>;"></div>
                                          <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold <?php echo $is_on_progress ? 'text-warning' : 'text-muted'; ?>">Laporan Diproses</span>
                                            <small class="text-muted"><?php echo $is_on_progress ? date('d M Y H:i', strtotime($row['waktu_on_progress'])) : '-'; ?></small>
                                          </div>
                                          <p class="text-muted small mb-0">Laporan disetujui dan saat ini sedang ditindaklanjuti oleh admin/dekanat.</p>
                                        </div>
                                        
                                        <!-- Node 3: Selesai -->
                                        <div class="position-relative">
                                          <?php 
                                          $is_resolve = (strcasecmp($status, 'resolve') == 0) && !is_null($row['waktu_resolve']); 
                                          $color_resolve = $is_resolve ? '#71dd37' : '#d9dee3';
                                          ?>
                                          <!-- Bullet point -->
                                          <div class="position-absolute" style="left: -25px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background-color: <?php echo $color_resolve; ?>;"></div>
                                          <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold <?php echo $is_resolve ? 'text-success' : 'text-muted'; ?>">Laporan Selesai</span>
                                            <small class="text-muted"><?php echo $is_resolve ? date('d M Y H:i', strtotime($row['waktu_resolve'])) : '-'; ?></small>
                                          </div>
                                          <p class="text-muted small mb-2">Pekerjaan selesai dilakukan.</p>
                                          
                                          <?php if ($is_resolve && !empty($row['tanggapan_admin'])): ?>
                                            <div class="alert alert-success py-2 px-3 mb-0 small mt-1">
                                              <strong>Tanggapan Admin:</strong><br/>
                                              <?php echo nl2br(htmlspecialchars($row['tanggapan_admin'])); ?>
                                              
                                              <?php if (!empty($row['foto_bukti_selesai'])): ?>
                                                <div class="mt-2 border-top pt-2">
                                                  <strong>Bukti Penanganan:</strong><br/>
                                                  <a href="uploads/<?php echo htmlspecialchars($row['foto_bukti_selesai']); ?>" target="_blank">
                                                    <img src="uploads/<?php echo htmlspecialchars($row['foto_bukti_selesai']); ?>" class="img-fluid rounded mt-1" style="max-height: 180px;" alt="Bukti Penanganan" />
                                                  </a>
                                                </div>
                                              <?php endif; ?>
                                            </div>
                                          <?php endif; ?>
                                        </div>

                                        <?php if (strcasecmp($status, 'ditolak') == 0): ?>
                                          <hr class="my-3" />
                                          <div class="alert alert-danger py-3 px-3 mb-0">
                                            <div class="d-flex align-items-start gap-2">
                                              <i class="bx bx-x-circle fs-5 text-danger flex-shrink-0 mt-1"></i>
                                              <div>
                                                <strong class="d-block mb-1">Laporan Ditolak oleh Admin</strong>
                                                <?php if (!empty($row['tanggapan_admin'])): ?>
                                                  <span class="small"><strong>Alasan:</strong> <?php echo nl2br(htmlspecialchars($row['tanggapan_admin'])); ?></span>
                                                <?php else: ?>
                                                  <span class="small text-muted">Tidak ada alasan yang dicantumkan.</span>
                                                <?php endif; ?>
                                              </div>
                                            </div>
                                          </div>
                                        <?php endif; ?>
                                        
                                      </div>

                                      <!-- Integrasi Rating -->
                                      <?php if ($is_resolve): ?>
                                        <hr class="my-3" />
                                        <div class="mt-3">
                                          <h6 class="fw-semibold mb-2">Penilaian Layanan:</h6>
                                           <?php if (is_null($row['rating'])): ?>
                                            <!-- Form Rating -->
                                            <form action="proses_rating.php" method="POST" class="p-3 bg-light rounded border">
                                              <input type="hidden" name="id_pengaduan" value="<?php echo htmlspecialchars($row['id_pengaduan']); ?>" />
                                              <div class="d-flex align-items-center gap-2 mb-2">
                                                <label for="rating-select-<?php echo $row['id_pengaduan']; ?>" class="form-label mb-0 small fw-bold">Berikan Rating:</label>
                                                <select id="rating-select-<?php echo $row['id_pengaduan']; ?>" name="rating" class="form-select form-select-sm" style="width: auto;" required>
                                                  <option value="" selected disabled>Pilih Rating</option>
                                                  <option value="1">1 ⭐ (Sangat Buruk)</option>
                                                  <option value="2">2 ⭐ (Buruk)</option>
                                                  <option value="3">3 ⭐ (Cukup)</option>
                                                  <option value="4">4 ⭐ (Baik)</option>
                                                  <option value="5">5 ⭐ (Sangat Baik)</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                              </div>
                                              <textarea name="komentar_mahasiswa" class="form-control form-control-sm mt-1" rows="2" placeholder="Tulis komentar/saran Anda..." required></textarea>
                                            </form>
                                          <?php else: ?>
                                            <!-- Badge Rating Selesai -->
                                            <div>
                                              <span class="badge bg-label-primary p-2 d-inline-flex align-items-center">
                                                <i class="bx bxs-star me-1 text-warning"></i>
                                                Rating Anda: <?php echo htmlspecialchars($row['rating']); ?> / 5 Bintang
                                              </span>
                                              <?php if (!empty($row['komentar_mahasiswa'])): ?>
                                                <div class="mt-2 text-muted fst-italic small">
                                                  "<?php echo htmlspecialchars($row['komentar_mahasiswa']); ?>"
                                                </div>
                                              <?php endif; ?>
                                            </div>
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
                          <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat pengaduan.</td>
                        </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Tabel Riwayat Pengaduan -->
            </div>
            <!-- / Content -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>
