<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$nrp = $_SESSION['nrp'];
$nrp_aman = mysqli_real_escape_string($koneksi, $nrp);

// Query Poin & Status Akademik Mahasiswa
$kueri_mhs = "SELECT poin, status_akademik FROM Mahasiswa WHERE nrp = '$nrp_aman'";
$result_mhs = mysqli_query($koneksi, $kueri_mhs);
$row_mhs = mysqli_fetch_assoc($result_mhs);
$poin_mahasiswa = $row_mhs['poin'] ?? 0;
$status_akademik = $row_mhs['status_akademik'] ?? 'Aktif';

// Query Stat Cards (Diajukan, Diproses, Selesai) combined
$tiket_pending = 0;
$tiket_proses = 0;
$tiket_resolve = 0;

$kueri_stats = "SELECT status, COUNT(*) AS total FROM Pengaduan WHERE nrp = '$nrp_aman' GROUP BY status";
$result_stats = mysqli_query($koneksi, $kueri_stats);
while ($row_stats = mysqli_fetch_assoc($result_stats)) {
    $status = strtolower($row_stats['status']);
    if ($status === 'pending') {
        $tiket_pending = $row_stats['total'];
    } elseif ($status === 'on progress') {
        $tiket_proses = $row_stats['total'];
    } elseif ($status === 'resolve') {
        $tiket_resolve = $row_stats['total'];
    }
}

// Query Action Required Alert (tickets resolved but not rated yet)
$kueri_unrated = "SELECT COUNT(*) AS total FROM Pengaduan WHERE nrp = '$nrp_aman' AND status = 'Resolve' AND rating IS NULL";
$result_unrated = mysqli_query($koneksi, $kueri_unrated);
$row_unrated = mysqli_fetch_assoc($result_unrated);
$unrated_count = $row_unrated['total'] ?? 0;

// Query Active Tickets (Pending / On Progress)
$kueri_aktif = "SELECT p.*, k.nama_kategori 
                FROM Pengaduan p
                LEFT JOIN Kategori k ON p.id_kategori = k.id_kategori
                WHERE p.nrp = '$nrp_aman' AND p.status IN ('Pending', 'On Progress')
                ORDER BY p.tanggal_lapor DESC 
                LIMIT 5";
$result_aktif = mysqli_query($koneksi, $kueri_aktif);

// Query Chart Category data for this student
$kueri_chart = "SELECT k.nama_kategori, COUNT(p.id_pengaduan) AS total
                FROM Kategori k
                JOIN Pengaduan p ON k.id_kategori = p.id_kategori
                WHERE p.nrp = '$nrp_aman'
                GROUP BY k.id_kategori, k.nama_kategori";
$result_chart = mysqli_query($koneksi, $kueri_chart);
$chart_labels = [];
$chart_series = [];
while ($row_chart = mysqli_fetch_assoc($result_chart)) {
    $chart_labels[] = $row_chart['nama_kategori'];
    $chart_series[] = (int) $row_chart['total'];
}
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
              
              <!-- Welcome Banner Card -->
              <div class="card bg-primary text-white mb-4">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                  <div>
                    <h4 class="card-title text-white mb-1">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_mahasiswa'] ?? 'Mahasiswa'); ?>!</h4>
                    <div class="d-flex align-items-center mb-2">
                      <span class="badge bg-warning text-dark fw-bold d-inline-flex align-items-center px-3 py-2 fs-6">
                        <i class="bx bxs-medal me-1 fs-5"></i> <?php echo number_format($poin_mahasiswa); ?> Poin Pengaduan
                      </span>
                    </div>
                    <p class="card-text mb-0">Ada kendala fasilitas atau layanan? Laporkan di sini!</p>
                  </div>
                  <a href="buat_tiket.php" class="btn btn-white bg-white text-primary fw-bold flex-shrink-0"><i class="bx bx-plus me-1"></i> Buat Pengaduan</a>
                </div>
              </div>

              <!-- Banner Action Required (Unrated Alert) -->
              <?php if ($unrated_count > 0): ?>
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center justify-content-between mb-4 p-4" role="alert">
                  <div class="d-flex align-items-center">
                    <i class="bx bxs-star me-2 fs-3 text-warning"></i>
                    <span>Anda memiliki <strong><?php echo $unrated_count; ?></strong> pengaduan yang telah diselesaikan. Mohon berikan penilaian agar kami dapat meningkatkan kualitas pelayanan!</span>
                  </div>
                  <a href="riwayat_mahasiswa.php" class="btn btn-sm btn-warning fw-bold ms-3">Beri Penilaian</a>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php endif; ?>
              
              <!-- Statistik Cards -->
              <div class="row mb-4">
                <!-- Tiket Pending -->
                <div class="col-lg-4 col-md-12 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-danger text-danger"><i class="bx bx-time bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Diajukan</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_pending; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Tiket On Progress -->
                <div class="col-lg-4 col-md-12 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-warning text-warning"><i class="bx bx-loader-circle bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Diproses</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_proses; ?></h4>
                    </div>
                  </div>
                </div>
                <!-- Tiket Resolved -->
                <div class="col-lg-4 col-md-12 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between mb-4">
                        <div class="avatar flex-shrink-0">
                          <span class="avatar-initial rounded bg-label-success text-success"><i class="bx bx-check-circle bx-lg"></i></span>
                        </div>
                      </div>
                      <p class="mb-1 fw-semibold">Selesai</p>
                      <h4 class="card-title mb-0"><?php echo $tiket_resolve; ?></h4>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Statistik Cards -->

              <!-- Main Content Grid -->
              <div class="row">
                <!-- Left Column: Tabel Tiket Aktif -->
                <div class="col-md-8 mb-4">
                  <div class="card h-100">
                    <h5 class="card-header fw-bold pb-2">Tiket Aktif</h5>
                    <div class="table-responsive text-nowrap">
                      <table class="table table-hover align-middle mb-0">
                        <thead>
                          <tr>
                            <th>ID Tiket</th>
                            <th>Detail Keluhan</th>
                            <th>Status Perkembangan</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                          <?php if (mysqli_num_rows($result_aktif) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result_aktif)): ?>
                              <?php $status = $row['status']; ?>
                              <tr>
                                <td><strong><?php echo htmlspecialchars($row['id_pengaduan']); ?></strong></td>
                                <td>
                                  <span class="d-inline-block text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($row['detail_keluhan']); ?>">
                                    <?php echo htmlspecialchars($row['detail_keluhan']); ?>
                                  </span>
                                </td>
                                <td>
                                  <!-- Mini Stepper / Progress Bar Horizontal -->
                                  <div class="d-flex align-items-center gap-1">
                                    <div class="d-flex align-items-center">
                                      <span class="badge bg-label-primary px-2 py-1" style="font-size: 0.75rem;"><i class="bx bx-check-circle me-1"></i>Diajukan</span>
                                    </div>
                                    <div class="h-px bg-light flex-grow-1" style="min-width: 15px; border-top: 2px dashed #d9dee3;"></div>
                                    <div class="d-flex align-items-center">
                                      <?php if ($status == 'On Progress'): ?>
                                        <span class="badge bg-warning text-white px-2 py-1" style="font-size: 0.75rem;"><i class="bx bx-loader-circle bx-spin me-1"></i>Diproses</span>
                                      <?php else: ?>
                                        <span class="badge bg-label-secondary px-2 py-1 text-muted" style="font-size: 0.75rem;"><i class="bx bx-time me-1"></i>Diproses</span>
                                      <?php endif; ?>
                                    </div>
                                    <div class="h-px bg-light flex-grow-1" style="min-width: 15px; border-top: 2px dashed #d9dee3;"></div>
                                    <div class="d-flex align-items-center">
                                      <span class="badge bg-label-secondary px-2 py-1 text-muted" style="font-size: 0.75rem;"><i class="bx bx-check me-1"></i>Selesai</span>
                                    </div>
                                  </div>
                                </td>
                                <td>
                                  <button class="btn btn-sm btn-outline-primary px-2" data-bs-toggle="modal" data-bs-target="#modalDetail<?php echo $row['id_pengaduan']; ?>">
                                    <i class="bx bx-time-five me-1"></i> Lacak
                                  </button>

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
                                            
                                            <!-- Node 1: Laporan Diajukan -->
                                            <div class="mb-4 position-relative">
                                              <!-- Bullet point -->
                                              <div class="position-absolute" style="left: -25px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background-color: #696cff;"></div>
                                              <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-primary">Laporan Diajukan</span>
                                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($row['tanggal_lapor'])); ?></small>
                                              </div>
                                              <p class="text-muted small mb-0">Pengaduan Anda telah terkirim dan masuk antrean sistem.</p>
                                            </div>
                                            
                                            <!-- Node 2: Laporan Diproses -->
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
                                            
                                            <!-- Node 3: Laporan Selesai -->
                                            <div class="position-relative">
                                              <!-- Bullet point -->
                                              <div class="position-absolute" style="left: -25px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background-color: #d9dee3;"></div>
                                              <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="fw-bold text-muted">Laporan Selesai</span>
                                                <small class="text-muted">-</small>
                                              </div>
                                              <p class="text-muted small mb-0">Pekerjaan selesai dilakukan.</p>
                                            </div>
                                            
                                          </div>

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
                              <td colspan="4" class="text-center text-muted py-5">
                                Tidak ada tiket aktif saat ini.
                              </td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="card-footer text-end border-top">
                      <a href="riwayat_mahasiswa.php" class="text-primary fw-bold small">Lihat Semua Riwayat <i class="bx bx-right-arrow-alt align-middle"></i></a>
                    </div>
                  </div>
                </div>

                <!-- Right Column: Chart Ringkasan Kategori -->
                <div class="col-md-4 mb-4">
                  <div class="card h-100">
                    <div class="card-header pb-0">
                      <h5 class="card-title mb-1 fw-bold">Ringkasan Kategori</h5>
                      <small class="text-muted">Proporsi pengaduan berdasarkan kategori</small>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                      <div id="kategoriDonutChart" style="width: 100%; min-height: 290px;"></div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Main Content Grid -->
            </div>
            <!-- / Content -->

            <!-- Footer & Scripts -->
            <?php include 'footer.php'; ?>

            <script>
              document.addEventListener('DOMContentLoaded', function () {
                // ApexCharts Donut Chart for Kategori
                const chartEl = document.querySelector('#kategoriDonutChart');
                if (chartEl) {
                  const chartLabels = <?php echo json_encode($chart_labels); ?>;
                  const chartSeries = <?php echo json_encode($chart_series); ?>;

                  if (chartSeries.length === 0) {
                    chartEl.innerHTML = '<div class="text-muted text-center py-5">Belum ada data pengaduan untuk grafik</div>';
                  } else {
                    const chartColors = ['#004b87', '#03c3ec', '#ffab00', '#ff3e1d', '#71dd37'];
                    const options = {
                      series: chartSeries,
                      labels: chartLabels,
                      chart: {
                        type: 'donut',
                        height: 270,
                        parentHeightOffset: 0
                      },
                      colors: chartColors.slice(0, chartSeries.length),
                      stroke: {
                        width: 5,
                        colors: ['#fff']
                      },
                      legend: {
                        show: true,
                        position: 'bottom',
                        horizontalAlign: 'center',
                        labels: {
                          colors: '#566a7f',
                          useSeriesColors: false
                        }
                      },
                      dataLabels: {
                        enabled: false
                      },
                      plotOptions: {
                        pie: {
                          donut: {
                            size: '70%',
                            labels: {
                              show: true,
                              value: {
                                show: true,
                                fontSize: '1.25rem',
                                fontFamily: 'Public Sans',
                                color: '#566a7f',
                                offsetY: -10,
                                formatter: function (val) {
                                  return parseInt(val) + ' Tiket';
                                }
                              },
                              total: {
                                show: true,
                                label: 'Total',
                                color: '#a1acb8',
                                fontSize: '0.85rem',
                                formatter: function (w) {
                                  return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' Tiket';
                                }
                              }
                            }
                          }
                        }
                      }
                    };

                    const chart = new ApexCharts(chartEl, options);
                    chart.render();
                  }
                }
              });
            </script>
          </div>
          <!-- / Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>
      <!-- / Layout wrapper -->
    </div>
  </body>
</html>
